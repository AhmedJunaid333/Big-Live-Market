<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Database\Seeders\TaxonomyAndGeographySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SearchAndEngagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TaxonomyAndGeographySeeder::class);
    }

    public function test_search_with_keyword_and_price_sorting(): void
    {
        $seller = User::factory()->create();

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'iPhone 15 Pro Max Natural Titanium',
            'description' => 'Factory unlocked 256GB with Apple warranty.',
            'price_minor' => 34000000,
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'iPhone 13 128GB Starlight',
            'description' => 'Original box, battery health 88%.',
            'price_minor' => 14000000,
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        // Search for iPhone with price_asc
        $response = $this->getJson('/api/v1/search?q=iPhone&sort=price_asc');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $this->assertEquals('iPhone 13 128GB Starlight', $response->json('data.0.title'));
        $this->assertEquals('iPhone 15 Pro Max Natural Titanium', $response->json('data.1.title'));
    }

    public function test_search_suggestions_autocomplete(): void
    {
        $seller = User::factory()->create();

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Honda Civic Oriel 2021',
            'description' => 'Sunroof edition in immaculate condition.',
            'price_minor' => 520000000,
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/search/suggestions?q=Civic');

        $response->assertStatus(200)
            ->assertJsonPath('data.listings.0', 'Honda Civic Oriel 2021');
    }

    public function test_map_discovery_applies_privacy_rounding(): void
    {
        $seller = User::factory()->create();

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Map Pin House',
            'description' => 'Beautiful property with coordinates.',
            'price_minor' => 1000000000,
            'condition_code' => 'new',
            'status' => 'active',
            'latitude' => 31.5204859,
            'longitude' => 74.3587123,
            'location_label' => 'Gulberg, Lahore',
        ]);

        $response = $this->getJson('/api/v1/search/map');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Bounded to 3 decimal places (privacy rounding)
        $this->assertEquals(31.52, round($response->json('data.0.latitude'), 2));
        $this->assertEquals(74.359, $response->json('data.0.longitude'));
    }

    public function test_user_can_favorite_and_unfavorite_listing(): void
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Favorited Item',
            'description' => 'Item to be favorited.',
            'price_minor' => 200000,
            'condition_code' => 'new',
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        // 1. Add favorite
        $this->postJson('/api/v1/favorites/' . $listing->id)
            ->assertStatus(200);

        $this->assertEquals(1, $listing->fresh()->favorites_count);

        // 2. Fetch favorites
        $listResponse = $this->getJson('/api/v1/favorites');
        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // 3. Remove favorite
        $this->deleteJson('/api/v1/favorites/' . $listing->id)
            ->assertStatus(200);

        $this->assertEquals(0, $listing->fresh()->favorites_count);
    }

    public function test_user_can_follow_seller_and_view_public_profile(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create(['name' => 'Verified Super Seller']);

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Seller Active Listing',
            'description' => 'First of many listings.',
            'condition_code' => 'new',
            'status' => 'active',
        ]);

        Sanctum::actingAs($buyer);

        // 1. Follow seller
        $this->postJson('/api/v1/sellers/' . $seller->id . '/follow')
            ->assertStatus(200);

        // 2. View public profile
        $profileResponse = $this->getJson('/api/v1/sellers/' . $seller->id);
        $profileResponse->assertStatus(200)
            ->assertJsonPath('data.seller.name', 'Verified Super Seller')
            ->assertJsonPath('data.seller.followers_count', 1)
            ->assertJsonPath('data.seller.active_listings_count', 1)
            ->assertJsonPath('data.seller.is_followed', true);
    }

    public function test_user_can_save_and_delete_search(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/saved-searches', [
            'name' => 'Toyota Lahore under 5M',
            'filters' => [
                'q' => 'Toyota',
                'city_id' => 1,
                'price_max' => 500000000,
            ],
        ]);

        $response->assertStatus(201);
        $searchId = $response->json('data.id');

        $this->assertDatabaseHas('saved_searches', [
            'id' => $searchId,
            'user_id' => $user->id,
            'name' => 'Toyota Lahore under 5M',
        ]);

        $this->deleteJson('/api/v1/saved-searches/' . $searchId)
            ->assertStatus(200);

        $this->assertDatabaseMissing('saved_searches', ['id' => $searchId]);
    }
}
