<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_health_check_returns_success_envelope(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'service' => 'Big Live Marketplace API',
                'version' => '1.0.0',
            ])
            ->assertJsonStructure([
                'status',
                'service',
                'version',
                'timestamp',
            ]);
    }
}
