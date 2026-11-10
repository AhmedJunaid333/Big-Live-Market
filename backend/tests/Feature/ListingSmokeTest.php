<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListingSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_listings(): void
    {
        $seller = User::factory()->create();

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Original Vintage Bicycle',
            'description' => 'A beautifully restored classic bicycle in excellent condition.',
            'price_minor' => 1500000,
            'currency' => 'PKR',
            'condition_code' => 'used',
            'status' => 'active',
            'location_label' => 'Lahore, Pakistan',
        ]);

        $response = $this->getJson('/api/v1/listings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['next_cursor'],
            ]);

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Original Vintage Bicycle', $response->json('data.0.title'));
    }

    public function test_authenticated_user_can_create_draft_listing(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'Brand New Wireless Headphones',
            'description' => 'Noise-cancelling wireless headphones with original packaging.',
            'category_id' => 2,
            'city_id' => 1,
            'price_minor' => 850000,
            'currency' => 'PKR',
            'condition_code' => 'new',
            'location_label' => 'Karachi, Pakistan',
        ];

        $response = $this->postJson('/api/v1/listings', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Brand New Wireless Headphones')
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.seller_id', $user->id);

        $this->assertDatabaseHas('listings', [
            'title' => 'Brand New Wireless Headphones',
            'status' => 'draft',
            'seller_id' => $user->id,
        ]);
    }
}
