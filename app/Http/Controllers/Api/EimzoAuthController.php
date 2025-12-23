<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EimzoService;
use App\Services\EimzoAuthService;
use App\Http\Requests\EimzoLoginRequest;
use App\Exceptions\EimzoException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * E-IMZO Authentication Controller
 * 
 * @package App\Http\Controllers\Api
 */
class EimzoAuthController extends Controller
{
    public function __construct(
        private readonly EimzoService $eimzoService,
        private readonly EimzoAuthService $authService
    ) {}

    /**
     * Get challenge for authentication
     * 
     * @return JsonResponse
     */
    public function getChallenge(): JsonResponse
    {
        try {
            $challenge = $this->eimzoService->generateChallenge();

            return $this->successResponse([
                'challenge' => $challenge['challenge'],
                'ttl' => $challenge['ttl'],
            ]);
        } catch (EimzoException $e) {
            Log::error('Challenge generation failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return $this->errorResponse(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    /**
     * Login with E-IMZO signature
     * 
     * @param EimzoLoginRequest $request
     * @return JsonResponse
     */
    public function login(EimzoLoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->input('pkcs7'),
                $request->ip()
            );

            // Create session
            auth()->login($result['user'], $request->boolean('remember', true));

            return $this->successResponse([
                'message' => 'Tizimga muvaffaqiyatli kirdingiz',
                'user' => $result['user']->only(['id', 'name', 'pinfl', 'inn']),
                'certificate' => $result['certificate'],
            ]);
        } catch (EimzoException $e) {
            Log::warning('Login failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Logout
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successResponse([
            'message' => 'Tizimdan muvaffaqiyatli chiqdingiz',
        ]);
    }

    /**
     * Get current user information
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Autentifikatsiya talab qilinadi', 401);
        }

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'pinfl', 'inn', 'certificate_serial']),
        ]);
    }

    /**
     * Success response helper
     * 
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    private function successResponse(array $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Error response helper
     * 
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
