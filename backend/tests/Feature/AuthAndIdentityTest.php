<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserDevice;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthAndIdentityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_user_can_register_and_receive_token(): void
    {
        $payload = [
            'name' => 'Ali Khan',
            'email' => 'ali.khan@example.com',
            'password' => 'SecurePass123!',
            'phone_e164' => '+923001234567',
            'locale' => 'ur',
            'preferred_currency' => 'PKR',
            'device_name' => 'Pixel 8',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.name', 'Ali Khan')
            ->assertJsonPath('data.user.email', 'ali.khan@example.com')
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', ['email' => 'ali.khan@example.com']);
        $this->assertDatabaseHas('user_consents', ['document_type' => 'terms_and_privacy']);
        $this->assertDatabaseHas('user_devices', ['device_name' => 'Pixel 8']);
    }

    public function test_duplicate_email_registration_fails(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Second User',
            'email' => 'duplicate@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'seller@example.com',
            'password' => 'Secret12345',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'seller@example.com',
            'password' => 'Secret12345',
            'device_name' => 'Web Browser',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', 'seller@example.com')
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'seller@example.com',
            'password' => 'Secret12345',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'seller@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('code', 'INVALID_CREDENTIALS');
    }

    public function test_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
    }

    public function test_user_can_logout_all_devices(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout-all');

        $response->assertStatus(200);
    }

    public function test_otp_generation_and_verification_flow(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // 1. Request OTP
        $sendResponse = $this->postJson('/api/v1/auth/otp/send', [
            'destination' => '+923009876543',
            'purpose' => 'phone_verification',
        ]);

        $sendResponse->assertStatus(200);
        $mockCode = $sendResponse->json('data.mock_code');
        $this->assertNotNull($mockCode);

        // 2. Verify OTP
        $verifyResponse = $this->postJson('/api/v1/auth/otp/verify', [
            'destination' => '+923009876543',
            'code' => $mockCode,
            'purpose' => 'phone_verification',
        ]);

        $verifyResponse->assertStatus(200);

        // 3. User phone verified timestamp is set
        $this->assertEquals('+923009876543', $user->fresh()->phone_e164);
        $this->assertNotNull($user->fresh()->phone_verified_at);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create(['name' => 'Profile User']);
        $user->assignRole('buyer');
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Profile User')
            ->assertJsonPath('data.roles.0.name', 'buyer');
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/me', [
            'name' => 'Updated Name',
            'locale' => 'ur',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.locale', 'ur');

        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_user_can_record_consent(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/consents', [
            'document_type' => 'privacy_policy',
            'version' => '2.1',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.document_type', 'privacy_policy')
            ->assertJsonPath('data.version', '2.1');

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'document_type' => 'privacy_policy',
            'version' => '2.1',
        ]);
    }

    public function test_user_can_submit_verification_request(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/verification', [
            'type' => 'cnic',
            'evidence_private_path' => 'private/verifications/user_1_cnic.jpg',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'cnic')
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('user_verifications', [
            'user_id' => $user->id,
            'type' => 'cnic',
            'status' => 'pending',
        ]);
    }

    public function test_duplicate_pending_verification_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // First verification request
        $this->postJson('/api/v1/me/verification', [
            'type' => 'cnic',
            'evidence_private_path' => 'private/verifications/user_1_cnic.jpg',
        ])->assertStatus(201);

        // Second verification request while first is still pending
        $second = $this->postJson('/api/v1/me/verification', [
            'type' => 'passport',
            'evidence_private_path' => 'private/verifications/user_1_passport.jpg',
        ]);

        $second->assertStatus(422)
            ->assertJsonPath('code', 'VERIFICATION_PENDING');
    }

    public function test_user_can_list_and_revoke_devices(): void
    {
        $user = User::factory()->create();
        $device = UserDevice::create([
            'user_id' => $user->id,
            'device_name' => 'Old Phone',
            'platform' => 'android',
        ]);

        Sanctum::actingAs($user);

        $listResponse = $this->getJson('/api/v1/me/devices');
        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $deleteResponse = $this->deleteJson('/api/v1/me/devices/' . $device->id);
        $deleteResponse->assertStatus(200);

        $this->assertNotNull($device->fresh()->revoked_at);
    }

    public function test_account_soft_deletion_and_session_cleanup(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/v1/me');

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertEquals('deleted', $user->fresh()->status);
    }
}
