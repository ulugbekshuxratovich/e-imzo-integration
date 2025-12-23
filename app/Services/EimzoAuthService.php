<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Exceptions\EimzoException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * E-IMZO Authentication Service
 * 
 * Handles user authentication and registration via E-IMZO
 * 
 * @package App\Services
 */
class EimzoAuthService
{
    public function __construct(
        private readonly EimzoService $eimzoService
    ) {}

    /**
     * Authenticate user with PKCS7 signature
     * 
     * @param string $pkcs7 Base64 encoded PKCS7
     * @param string $userIp User's IP address
     * @return array{user: User, certificate: array}
     * @throws EimzoException
     */
    public function login(string $pkcs7, string $userIp): array
    {
        // Verify PKCS7 signature
        $authResult = $this->eimzoService->verifyAuth($pkcs7, $userIp);

        // Find or create user
        $user = $this->findOrCreateUser($authResult['subjectCertificateInfo']);

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $userIp,
        ]);

        return [
            'user' => $user,
            'certificate' => $authResult['subjectCertificateInfo'],
        ];
    }

    /**
     * Find existing user or create new one
     * 
     * @param array $certificateInfo Certificate information from E-IMZO
     * @return User
     * @throws EimzoException
     */
    public function findOrCreateUser(array $certificateInfo): User
    {
        $pinfl = $certificateInfo['subjectName']['1.2.860.3.16.1.2'] ?? null;
        $inn = $certificateInfo['subjectName']['UID'] ?? null;
        $fullName = $certificateInfo['subjectName']['CN'] ?? 'Unknown User';
        $serialNumber = $certificateInfo['serialNumber'];

        // Validate required fields
        if (!$pinfl && !$inn) {
            throw new EimzoException('PINFL yoki INN topilmadi sertifikatda');
        }

        // Find user by PINFL or INN
        $user = User::where(function ($query) use ($pinfl, $inn) {
            if ($pinfl) {
                $query->where('pinfl', $pinfl);
            }
            if ($inn) {
                $query->orWhere('inn', $inn);
            }
        })->first();

        if (!$user) {
            $user = $this->createUser($pinfl, $inn, $fullName, $serialNumber);
        } else {
            $this->updateUserCertificate($user, $fullName, $serialNumber);
        }

        return $user->fresh();
    }

    /**
     * Create new user
     * 
     * @param string|null $pinfl
     * @param string|null $inn
     * @param string $fullName
     * @param string $serialNumber
     * @return User
     */
    private function createUser(
        ?string $pinfl,
        ?string $inn,
        string $fullName,
        string $serialNumber
    ): User {
        return User::create([
            'name' => $fullName,
            'pinfl' => $pinfl,
            'inn' => $inn,
            'certificate_serial' => $serialNumber,
            'email' => $this->generateEmail($pinfl, $inn),
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Update existing user's certificate info
     * 
     * @param User $user
     * @param string $fullName
     * @param string $serialNumber
     * @return void
     */
    private function updateUserCertificate(
        User $user,
        string $fullName,
        string $serialNumber
    ): void {
        $user->update([
            'name' => $fullName,
            'certificate_serial' => $serialNumber,
        ]);
    }

    /**
     * Generate email from PINFL or INN
     * 
     * @param string|null $pinfl
     * @param string|null $inn
     * @return string
     */
    private function generateEmail(?string $pinfl, ?string $inn): string
    {
        $identifier = $pinfl ?? $inn;
        return "{$identifier}@eimzo.local";
    }

    /**
     * Get user certificate information
     * 
     * @param User $user
     * @return array|null
     */
    public function getUserCertificateInfo(User $user): ?array
    {
        if (!$user->certificate_serial) {
            return null;
        }

        return [
            'serial_number' => $user->certificate_serial,
            'pinfl' => $user->pinfl,
            'inn' => $user->inn,
            'name' => $user->name,
        ];
    }
}
