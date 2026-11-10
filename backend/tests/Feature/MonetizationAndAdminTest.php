<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\PromotionProduct;
use App\Models\Report;
use App\Models\User;
use App\Models\UserVerification;
use Database\Seeders\MonetizationSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\TaxonomyAndGeographySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MonetizationAndAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(TaxonomyAndGeographySeeder::class);
        $this->seed(MonetizationSeeder::class);
    }

    public function test_user_can_create_business_and_manage_team(): void
    {
        $owner = User::factory()->create();
        $agent = User::factory()->create(['email' => 'agent@dealer.com']);

        Sanctum::actingAs($owner);

        // 1. Create business
        $response = $this->postJson('/api/v1/businesses', [
            'name' => 'Lahore Motors Hub',
            'description' => 'Certified pre-owned Japanese cars in Lahore.',
        ]);

        $response->assertStatus(201);
        $businessId = $response->json('data.id');
        $slug = $response->json('data.slug');

        $this->assertTrue($owner->fresh()->hasRole('business_seller'));

        // 2. Add team member
        $memberResponse = $this->postJson('/api/v1/businesses/' . $businessId . '/members', [
            'email' => 'agent@dealer.com',
            'role' => 'agent',
        ]);
        $memberResponse->assertStatus(200);

        // 3. Public storefront view
        $storeResponse = $this->getJson('/api/v1/businesses/' . $slug);
        $storeResponse->assertStatus(200)
            ->assertJsonPath('data.storefront.name', 'Lahore Motors Hub');
    }

    public function test_promotions_checkout_and_webhook_activation(): void
    {
        $seller = User::factory()->create();
        Sanctum::actingAs($seller);

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Car for Promotion',
            'description' => 'Detailed car description.',
            'price_minor' => 200000000,
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        // 1. Checkout promotion
        $checkoutResponse = $this->postJson('/api/v1/promotions/checkout', [
            'listing_id' => $listing->id,
            'product_code' => 'featured_7d',
            'idempotency_key' => 'idemp_promo_12345',
        ]);

        $checkoutResponse->assertStatus(201);
        $orderId = $checkoutResponse->json('data.order.id');
        $paymentId = $checkoutResponse->json('data.payment.provider_payment_id');

        // 2. Simulate Webhook call from payment gateway
        $webhookResponse = $this->postJson('/api/v1/payments/mock_gateway/webhook', [
            'event_id' => 'evt_998877',
            'provider_payment_id' => $paymentId,
            'status' => 'paid',
        ], [
            'X-Webhook-Signature' => 'test_sig',
        ]);

        $webhookResponse->assertStatus(200);

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'paid']);
        $this->assertDatabaseHas('promotions', ['listing_id' => $listing->id, 'status' => 'active']);
    }

    public function test_admin_review_queue_and_listing_approval(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $seller = User::factory()->create();
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Pending Review Item',
            'description' => 'Item waiting for moderation approval.',
            'price_minor' => 150000,
            'condition_code' => 'new',
            'status' => 'pending_review',
        ]);

        Sanctum::actingAs($admin);

        // 1. Check review queue
        $queueResponse = $this->getJson('/api/v1/admin/listings/review');
        $queueResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // 2. Approve listing
        $approveResponse = $this->postJson('/api/v1/admin/listings/' . $listing->id . '/approve');
        $approveResponse->assertStatus(200);

        $this->assertEquals('active', $listing->fresh()->status);
        $this->assertDatabaseHas('moderation_actions', [
            'target_id' => $listing->id,
            'action' => 'approve',
        ]);
    }

    public function test_admin_user_verification_approval_grants_badge(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $verification = UserVerification::create([
            'user_id' => $user->id,
            'type' => 'cnic',
            'evidence_private_path' => 'private/docs/cnic.jpg',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/admin/verifications/' . $verification->id . '/decision', [
            'decision' => 'approved',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('approved', $verification->fresh()->status);
        $this->assertTrue($user->fresh()->hasRole('verified_seller'));
    }

    public function test_user_can_submit_report_and_admin_resolves_it(): void
    {
        $reporter = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($reporter);

        // Submit report
        $reportResponse = $this->postJson('/api/v1/reports', [
            'target_type' => 'listing',
            'target_id' => 42,
            'reason_code' => 'spam',
            'details' => 'This ad contains spam keywords repeatedly.',
        ]);

        $reportResponse->assertStatus(201);
        $reportId = $reportResponse->json('data.id');

        // Admin resolves report
        Sanctum::actingAs($admin);
        $resolveResponse = $this->postJson('/api/v1/admin/reports/' . $reportId . '/resolve', [
            'action' => 'warn_user',
            'reason' => 'First warning issued for spamming tags.',
        ]);

        $resolveResponse->assertStatus(200);
        $this->assertDatabaseHas('reports', ['id' => $reportId, 'status' => 'resolved']);
    }
}
