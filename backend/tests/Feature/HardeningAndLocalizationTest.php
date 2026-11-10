<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class HardeningAndLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_responses_include_hardening_security_headers(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Strict-Transport-Security');
    }

    public function test_locale_middleware_respects_x_locale_header(): void
    {
        $enResponse = $this->getJson('/api/v1/health', ['X-Locale' => 'en']);
        $enResponse->assertStatus(200)
            ->assertHeader('Content-Language', 'en');

        $urResponse = $this->getJson('/api/v1/health', ['X-Locale' => 'ur']);
        $urResponse->assertStatus(200)
            ->assertHeader('Content-Language', 'ur');
    }

    public function test_translations_load_correctly_for_english_and_urdu(): void
    {
        App::setLocale('en');
        $this->assertEquals('Welcome to Big Live Marketplace', __('messages.welcome'));

        App::setLocale('ur');
        $this->assertEquals('بگ لائیو مارکیٹ پلیس میں خوش آمدید', __('messages.welcome'));
    }
}
