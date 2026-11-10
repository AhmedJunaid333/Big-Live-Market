<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\User;
use Database\Seeders\TaxonomyAndGeographySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatAndOffersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TaxonomyAndGeographySeeder::class);
    }

    public function test_buyer_can_start_conversation_and_send_messages(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Car for Chat Test',
            'description' => 'Test listing for conversation.',
            'price_minor' => 1000000,
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        Sanctum::actingAs($buyer);

        // 1. Initialize conversation
        $initResponse = $this->postJson('/api/v1/conversations', [
            'listing_id' => $listing->id,
            'initial_message' => 'Is this still available?',
        ]);

        $initResponse->assertStatus(201);
        $convId = $initResponse->json('data.id');

        // 2. Send additional message
        $msgResponse = $this->postJson('/api/v1/conversations/' . $convId . '/messages', [
            'body' => 'Can we meet today?',
        ]);

        $msgResponse->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $convId,
            'sender_id' => $buyer->id,
            'body' => 'Can we meet today?',
        ]);

        // 3. Seller marks read
        Sanctum::actingAs($seller);
        $readResponse = $this->postJson('/api/v1/conversations/' . $convId . '/read');
        $readResponse->assertStatus(200);

        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $convId,
            'sender_id' => $buyer->id,
            'read_at' => null,
        ]);
    }

    public function test_offer_counter_and_accept_lifecycle(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Negotiable Laptop',
            'description' => 'Great laptop negotiable price.',
            'price_minor' => 20000000, // 200,000 PKR
            'condition_code' => 'used',
            'status' => 'active',
        ]);

        $conversation = Conversation::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        // 1. Buyer makes offer: 180,000 PKR
        Sanctum::actingAs($buyer);
        $offerResponse = $this->postJson('/api/v1/conversations/' . $conversation->id . '/offers', [
            'amount_minor' => 18000000,
        ]);

        $offerResponse->assertStatus(201);
        $offerId = $offerResponse->json('data.id');

        // 2. Seller counters with 190,000 PKR
        Sanctum::actingAs($seller);
        $counterResponse = $this->postJson('/api/v1/offers/' . $offerId . '/counter', [
            'amount_minor' => 19000000,
        ]);

        $counterResponse->assertStatus(201);
        $counterOfferId = $counterResponse->json('data.id');
        $this->assertDatabaseHas('offers', ['id' => $offerId, 'status' => 'countered']);

        // 3. Buyer accepts counter offer
        Sanctum::actingAs($buyer);
        $acceptResponse = $this->postJson('/api/v1/offers/' . $counterOfferId . '/accept');
        $acceptResponse->assertStatus(200);
        $this->assertDatabaseHas('offers', ['id' => $counterOfferId, 'status' => 'accepted']);
    }

    public function test_user_blocking_prevents_chat(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'Block Test Listing',
            'description' => 'Listing for block test.',
            'price_minor' => 10000,
            'condition_code' => 'new',
            'status' => 'active',
        ]);

        // Seller blocks buyer
        Sanctum::actingAs($seller);
        $this->postJson('/api/v1/users/' . $buyer->id . '/block')
            ->assertStatus(200);

        // Buyer tries to message seller
        Sanctum::actingAs($buyer);
        $response = $this->postJson('/api/v1/conversations', [
            'listing_id' => $listing->id,
            'initial_message' => 'Hello?',
        ]);

        $response->assertStatus(403);
    }
}
