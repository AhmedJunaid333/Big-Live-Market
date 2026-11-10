<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Database\Seeders\TaxonomyAndGeographySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TaxonomyAndGeographySeeder::class);
    }

    public function test_seller_can_create_draft_and_attach_attributes(): void
    {
        $seller = User::factory()->create();
        Sanctum::actingAs($seller);

        $cars = Category::where('slug', 'cars')->first();
        $lahore = City::where('slug', 'lahore')->first();
        $yearAttr = CategoryAttribute::where('category_id', $cars->id)->where('code', 'year')->first();

        // 1. Create draft
        $response = $this->postJson('/api/v1/listings', [
            'title' => 'Toyota Corolla GLi 2022',
            'description' => 'First owner, bumper to bumper original, Lahore registered.',
            'category_id' => $cars->id,
            'city_id' => $lahore->id,
            'price_minor' => 450000000, // 4.5M PKR
            'currency' => 'PKR',
            'condition_code' => 'used',
        ]);

        $response->assertStatus(201);
        $listingId = $response->json('data.id');

        // 2. Attach attributes
        $attrResponse = $this->postJson('/api/v1/listings/' . $listingId . '/attributes', [
            'attributes' => [
                ['attribute_id' => $yearAttr->id, 'value' => 2022],
            ]
        ]);

        $attrResponse->assertStatus(200);
        $this->assertDatabaseHas('listing_attribute_values', [
            'listing_id' => $listingId,
            'category_attribute_id' => $yearAttr->id,
            'value_number' => 2022,
        ]);
    }

    public function test_listing_state_machine_transitions(): void
    {
        $seller = User::factory()->create();
        Sanctum::actingAs($seller);

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Sample Product for State Test',
            'description' => 'Detailed product description with enough characters for testing.',
            'price_minor' => 500000,
            'condition_code' => 'used',
            'status' => 'draft',
        ]);

        // 1. Submit for review (draft -> pending_review)
        $this->postJson('/api/v1/listings/' . $listing->id . '/submit')
            ->assertStatus(200);
        $this->assertEquals('pending_review', $listing->fresh()->status);

        // Simulate admin approval
        $listing->fresh()->transitionTo('active');
        $this->assertEquals('active', $listing->fresh()->status);

        // 2. Pause listing (active -> paused)
        $this->postJson('/api/v1/listings/' . $listing->id . '/pause')
            ->assertStatus(200);
        $this->assertEquals('paused', $listing->fresh()->status);

        // 3. Resume listing (paused -> active)
        $this->postJson('/api/v1/listings/' . $listing->id . '/resume')
            ->assertStatus(200);
        $this->assertEquals('active', $listing->fresh()->status);

        // 4. Mark as sold (active -> sold)
        $this->postJson('/api/v1/listings/' . $listing->id . '/mark-sold')
            ->assertStatus(200);
        $this->assertEquals('sold', $listing->fresh()->status);

        // 5. Verify audit history recorded
        $this->assertGreaterThanOrEqual(4, $listing->statusHistory()->count());
    }

    public function test_idor_protection_prevents_unauthorized_edits(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $listing = Listing::create([
            'seller_id' => $owner->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Protected Listing',
            'description' => 'Description for protected item.',
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        Sanctum::actingAs($attacker);

        // Attacker attempts to pause owner's listing
        $response = $this->postJson('/api/v1/listings/' . $listing->id . '/pause');
        $response->assertStatus(403);
    }

    public function test_seller_can_upload_media_to_listing(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create();
        Sanctum::actingAs($seller);

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Listing with Photo',
            'description' => 'Detailed product description with photo.',
            'condition_code' => 'new',
            'status' => 'draft',
        ]);

        $file = UploadedFile::fake()->image('corolla.jpg', 800, 600);

        $response = $this->postJson('/api/v1/listings/' . $listing->id . '/media', [
            'image' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('listing_media', [
            'listing_id' => $listing->id,
            'is_cover' => true,
        ]);
    }
}
