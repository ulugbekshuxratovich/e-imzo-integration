<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\EimzoException;

/**
 * Base E-IMZO Service
 * 
 * Handles communication with E-IMZO Server
 * 
 * @package App\Services
 * @author Your Name <your@email.com>
 */
class EimzoService
{
    private Client $client;
    private string $baseUrl;
    private string $frontendUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.eimzo.server_url');
        $this->frontendUrl = config('services.eimzo.frontend_url');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('services.eimzo.timeout', 30),
            'verify' => config('app.env') === 'production',
            'http_errors' => false,
        ]);
    }

    /**
     * Generate challenge for authentication
     * 
     * @return array{challenge: string, ttl: int}
     * @throws EimzoException
     */
    public function generateChallenge(): array
    {
        try {
            $response = $this->client->post('/frontend/challenge');
            $data = $this->parseResponse($response);

            if ($data['status'] !== 1) {
                throw new EimzoException(
                    $data['message'] ?? 'Challenge generation failed',
                    $data['status']
                );
            }

            // Save to Redis with TTL
            $this->saveChallengeToRedis($data['challenge'], $data['ttl']);

            return [
                'challenge' => $data['challenge'],
                'ttl' => $data['ttl'],
            ];
        } catch (GuzzleException $e) {
            Log::error('E-IMZO Challenge Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new EimzoException('Failed to connect to E-IMZO server', 0, $e);
        }
    }

    /**
     * Verify authentication PKCS7
     * 
     * @param string $pkcs7 Base64 encoded PKCS7
     * @param string $userIp User's IP address
     * @return array Certificate and status information
     * @throws EimzoException
     */
    public function verifyAuth(string $pkcs7, string $userIp): array
    {
        try {
            $response = $this->client->post('/backend/auth', [
                'body' => $pkcs7,
                'headers' => $this->getHeaders($userIp),
            ]);

            $data = $this->parseResponse($response);

            if ($data['status'] !== 1) {
                throw new EimzoException(
                    $this->getAuthErrorMessage($data['status']),
                    $data['status']
                );
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('E-IMZO Auth Verification Error', [
                'error' => $e->getMessage(),
                'ip' => $userIp,
            ]);
            
            throw new EimzoException('Authentication verification failed', 0, $e);
        }
    }

    /**
     * Add timestamp to PKCS7
     * 
     * @param string $pkcs7 Base64 encoded PKCS7
     * @param string $userIp User's IP address
     * @return array PKCS7 with timestamp
     * @throws EimzoException
     */
    public function addTimestamp(string $pkcs7, string $userIp): array
    {
        try {
            $response = $this->client->post('/frontend/timestamp/pkcs7', [
                'body' => $pkcs7,
                'headers' => $this->getHeaders($userIp),
            ]);

            $data = $this->parseResponse($response);

            if ($data['status'] !== 1) {
                throw new EimzoException(
                    $data['message'] ?? 'Failed to add timestamp',
                    $data['status']
                );
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('E-IMZO Timestamp Error', [
                'error' => $e->getMessage(),
                'ip' => $userIp,
            ]);
            
            throw new EimzoException('Failed to add timestamp', 0, $e);
        }
    }

    /**
     * Verify PKCS7 Attached signature
     * 
     * @param string $pkcs7 Base64 encoded PKCS7 Attached
     * @param string $userIp User's IP address
     * @return array Verification result
     * @throws EimzoException
     */
    public function verifyPkcs7Attached(string $pkcs7, string $userIp): array
    {
        try {
            $response = $this->client->post('/backend/pkcs7/verify/attached', [
                'body' => $pkcs7,
                'headers' => $this->getHeaders($userIp),
            ]);

            $data = $this->parseResponse($response);

            if ($data['status'] !== 1) {
                throw new EimzoException(
                    $this->getVerificationErrorMessage($data['status']),
                    $data['status']
                );
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('E-IMZO PKCS7 Verification Error', [
                'error' => $e->getMessage(),
                'ip' => $userIp,
            ]);
            
            throw new EimzoException('Signature verification failed', 0, $e);
        }
    }

    /**
     * Verify PKCS7 Detached signature
     * 
     * @param string $document Base64 encoded original document
     * @param string $pkcs7 Base64 encoded PKCS7 Detached
     * @param string $userIp User's IP address
     * @return array Verification result
     * @throws EimzoException
     */
    public function verifyPkcs7Detached(string $document, string $pkcs7, string $userIp): array
    {
        try {
            $body = "{$document}|{$pkcs7}";
            
            $response = $this->client->post('/backend/pkcs7/verify/detached', [
                'body' => $body,
                'headers' => $this->getHeaders($userIp),
            ]);

            $data = $this->parseResponse($response);

            if ($data['status'] !== 1) {
                throw new EimzoException(
                    $this->getVerificationErrorMessage($data['status']),
                    $data['status']
                );
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('E-IMZO PKCS7 Detached Verification Error', [
                'error' => $e->getMessage(),
                'ip' => $userIp,
            ]);
            
            throw new EimzoException('Detached signature verification failed', 0, $e);
        }
    }

    /**
     * Parse HTTP response to array
     * 
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     * @throws EimzoException
     */
    private function parseResponse($response): array
    {
        $statusCode = $response->getStatusCode();
        
        if ($statusCode >= 500) {
            throw new EimzoException('E-IMZO server error', $statusCode);
        }

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EimzoException('Invalid JSON response from E-IMZO server');
        }

        return $data;
    }

    /**
     * Get HTTP headers for request
     * 
     * @param string $userIp
     * @return array
     */
    private function getHeaders(string $userIp): array
    {
        return [
            'X-Real-IP' => $userIp,
            'Host' => parse_url($this->frontendUrl, PHP_URL_HOST),
        ];
    }

    /**
     * Save challenge to Redis
     * 
     * @param string $challenge
     * @param int $ttl Time to live in seconds
     * @return void
     */
    private function saveChallengeToRedis(string $challenge, int $ttl): void
    {
        $key = "eimzo:challenge:{$challenge}";
        $value = json_encode([
            'challenge' => $challenge,
            'created_at' => now()->toIso8601String(),
        ]);

        Redis::setex($key, $ttl, $value);
    }

    /**
     * Get authentication error message by status code
     * 
     * @param int $status
     * @return string
     */
    private function getAuthErrorMessage(int $status): string
    {
        return match ($status) {
            -1 => 'Sertifikat holatini tekshirib bo\'lmadi',
            -5 => 'Imzo vaqti noto\'g\'ri. Kompyuter vaqtini tekshiring',
            -10 => 'Elektron raqamli imzo noto\'g\'ri',
            -11 => 'Sertifikat noto\'g\'ri',
            -12 => 'Sertifikat imzo sanasida noto\'g\'ri',
            -20 => 'Challenge topilmadi yoki muddati tugagan',
            default => "Noma'lum xatolik (status: {$status})",
        };
    }

    /**
     * Get verification error message by status code
     * 
     * @param int $status
     * @return string
     */
    private function getVerificationErrorMessage(int $status): string
    {
        return match ($status) {
            -1 => 'Sertifikat holatini tekshirib bo\'lmadi',
            -10 => 'Elektron raqamli imzo noto\'g\'ri',
            -11 => 'Sertifikat noto\'g\'ri',
            -12 => 'Sertifikat imzo sanasida noto\'g\'ri',
            -20 => 'Timestamp sertifikatini tekshirib bo\'lmadi',
            -21 => 'Timestamp imzosi yoki heshi noto\'g\'ri',
            -22 => 'Timestamp sertifikati noto\'g\'ri',
            -23 => 'Timestamp sertifikati imzo sanasida noto\'g\'ri',
            default => "Noma'lum xatolik (status: {$status})",
        };
    }
}
