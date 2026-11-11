<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Models\UserConsent;
use App\Models\UserDevice;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Register a new user account.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone_e164' => $validated['phone_e164'] ?? null,
            'locale' => $validated['locale'] ?? 'en',
            'preferred_currency' => $validated['preferred_currency'] ?? 'PKR',
        ]);

        // Role assignment: buyer or seller
        $role = $validated['role'] ?? 'buyer';
        $user->assignRole($role);

        // Record initial Terms of Service consent
        UserConsent::create([
            'user_id' => $user->id,
            'document_type' => 'terms_and_privacy',
            'version' => '1.0',
            'accepted_at' => Carbon::now(),
            'ip_hash' => hash('sha256', (string) $request->ip()),
        ]);

        $deviceName = $validated['device_name'] ?? 'Mobile Device';
        $token = $user->createToken($deviceName)->plainTextToken;

        // Register device
        UserDevice::create([
            'user_id' => $user->id,
            'device_name' => $deviceName,
            'device_fingerprint_hash' => hash('sha256', $deviceName . (string) $request->userAgent()),
            'platform' => $request->header('X-Platform', 'flutter'),
            'last_seen_at' => Carbon::now(),
        ]);

        return $this->successResponse([
            'user' => $user->load('roles'),
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * Login with email or phone + password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $userQuery = User::query();
        if (!empty($validated['email'])) {
            $userQuery->where('email', $validated['email']);
        } elseif (!empty($validated['phone_e164'])) {
            $userQuery->where('phone_e164', $validated['phone_e164']);
        }

        $user = $userQuery->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials provided', 'INVALID_CREDENTIALS', [
                'auth' => ['The provided credentials do not match our records.'],
            ], 401);
        }

        if ($user->status === 'suspended') {
            return $this->errorResponse('Account has been suspended', 'ACCOUNT_SUSPENDED', [], 403);
        }

        $deviceName = $validated['device_name'] ?? 'Authorized Device';
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->update(['last_seen_at' => Carbon::now()]);

        UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_name' => $deviceName,
            ],
            [
                'platform' => $request->header('X-Platform', 'flutter'),
                'last_seen_at' => Carbon::now(),
                'revoked_at' => null,
            ]
        );

        return $this->successResponse([
            'user' => $user->load('roles'),
            'token' => $token,
        ], 'Logged in successfully');
    }

    /**
     * Logout and revoke current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Successfully logged out and revoked current access token');
    }

    /**
     * Logout and revoke all access tokens across all devices.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->devices()->update(['revoked_at' => Carbon::now()]);

        return $this->successResponse(null, 'Successfully logged out of all devices and revoked all tokens');
    }

    /**
     * Dispatch an OTP challenge.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $code = $this->otpService->generateChallenge(
            $validated['destination'],
            $validated['purpose'],
            $request->user()
        );

        return $this->successResponse([
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            // In testing/sandbox, we return the mock code so tests can verify
            'mock_code' => config('app.env') === 'production' ? null : $code,
        ], 'OTP challenge generated successfully');
    }

    /**
     * Verify an OTP challenge.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->otpService->verifyCode($validated['destination'], $validated['code'], $validated['purpose']);

        if ($validated['purpose'] === 'phone_verification' && $request->user()) {
            $request->user()->update([
                'phone_e164' => $validated['destination'],
                'phone_verified_at' => Carbon::now(),
            ]);
        }

        return $this->successResponse(null, 'Verification code validated successfully');
    }

    /**
     * Get authenticated user profile with roles and latest verification.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'latestVerification']);

        return $this->successResponse($user);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'username' => ['sometimes', 'nullable', 'string', 'max:60', 'unique:users,username,' . $user->id],
            'locale' => ['sometimes', 'string', 'in:en,ur'],
            'preferred_currency' => ['sometimes', 'string', 'size:3'],
        ]);

        $user->update($validated);

        return $this->successResponse($user->fresh(), 'Profile updated successfully');
    }

    /**
     * Soft delete user account per privacy & retention policy.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->devices()->update(['revoked_at' => Carbon::now()]);
        $user->update(['status' => 'deleted']);
        $user->delete();

        return $this->successResponse(null, 'Account has been scheduled for deletion and all active sessions revoked');
    }
}
