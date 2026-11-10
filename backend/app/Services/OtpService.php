<?php

namespace App\Services;

use App\Models\OtpChallenge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    protected const TTL_MINUTES = 10;
    protected const MAX_ATTEMPTS = 5;

    /**
     * Generate and record a new OTP challenge.
     */
    public function generateChallenge(string $destination, string $purpose = 'phone_verification', ?User $user = null): string
    {
        $destinationHash = hash('sha256', trim($destination));
        
        // Invalidate any existing pending challenges for this destination & purpose
        OtpChallenge::where('destination_hash', $destinationHash)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->delete();

        // 6-digit cryptographically random code
        $code = (string) random_int(100000, 999999);
        $codeHash = hash('sha256', $code);

        OtpChallenge::create([
            'user_id' => $user?->id,
            'destination_hash' => $destinationHash,
            'purpose' => $purpose,
            'code_hash' => $codeHash,
            'expires_at' => Carbon::now()->addMinutes(self::TTL_MINUTES),
            'attempts' => 0,
        ]);

        // In a real environment with SMS/email provider, the raw $code is dispatched.
        return $code;
    }

    /**
     * Verify an OTP challenge.
     */
    public function verifyCode(string $destination, string $code, string $purpose = 'phone_verification'): bool
    {
        $destinationHash = hash('sha256', trim($destination));
        $codeHash = hash('sha256', trim($code));

        $challenge = OtpChallenge::where('destination_hash', $destinationHash)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$challenge) {
            throw ValidationException::withMessages([
                'code' => ['No active OTP request found for this destination.'],
            ]);
        }

        if ($challenge->isExpired()) {
            throw ValidationException::withMessages([
                'code' => ['The verification code has expired. Please request a new one.'],
            ]);
        }

        if ($challenge->attempts >= self::MAX_ATTEMPTS) {
            throw ValidationException::withMessages([
                'code' => ['Maximum verification attempts reached. Please request a new code.'],
            ]);
        }

        $challenge->increment('attempts');

        if (!hash_equals($challenge->code_hash, $codeHash)) {
            throw ValidationException::withMessages([
                'code' => ['The verification code provided is incorrect.'],
            ]);
        }

        $challenge->update(['consumed_at' => Carbon::now()]);

        return true;
    }
}
