<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::create([
            'name' => 'Pakistan',
            'iso2' => 'PK',
            'iso3' => 'PAK',
            'phone_code' => '92',
            'currency_code' => 'PKR',
        ]);

        $region = Region::create([
            'country_id' => $country->id,
            'name' => 'Punjab',
            'code' => 'PB',
        ]);

        $city = City::create([
            'region_id' => $region->id,
            'name' => 'Lahore',
            'slug' => 'lahore',
        ]);

        $category = Category::create([
            'name' => 'Cars',
            'slug' => 'cars',
            'icon' => 'car',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $seller = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Test Seller',
            'email' => 'seller@biglive.pk',
            'password' => bcrypt('Secret123!'),
            'status' => 'active',
            'preferred_currency' => 'PKR',
        ]);

        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Honda Civic 2022',
            'slug' => 'honda-civic-2022-webtest',
            'description' => 'Test listing description for web testing verification.',
            'price_minor' => 450000000,
            'currency' => 'PKR',
            'condition_code' => 'used',
            'location_label' => 'Lahore, Pakistan',
            'status' => 'active',
            'published_at' => now(),
        ]);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Big Live');
    }

    public function test_login_and_register_pages_load(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('Sign in');
        $this->get('/register')->assertStatus(200)->assertSee('Create your free account');
    }

    public function test_sell_page_loads(): void
    {
        $this->get('/sell')->assertRedirect('/login');

        $seller = User::first();
        $this->actingAs($seller)->get('/sell')->assertStatus(200)->assertSee('POST YOUR AD');
    }

    public function test_search_and_category_pages_load(): void
    {
        $this->get('/search')->assertStatus(200);
        $this->get('/category/cars')->assertStatus(200)->assertSee('Cars');
    }

    public function test_listing_detail_page_loads(): void
    {
        $listing = Listing::first();
        $this->get('/listings/' . $listing->id)->assertStatus(200)->assertSee('Honda Civic 2022');
    }

    public function test_content_pages_load_without_404(): void
    {
        $this->get('/terms')->assertStatus(200)->assertSee('Terms of Service');
        $this->get('/privacy')->assertStatus(200)->assertSee('Privacy Policy');
        $this->get('/about')->assertStatus(200)->assertSee('About Big Live Marketplace');
        $this->get('/help')->assertStatus(200)->assertSee('Help & Customer Support');
        $this->get('/safety')->assertStatus(200)->assertSee('Safety & Fraud Prevention Tips');
    }

    public function test_missing_page_returns_custom_404(): void
    {
        $response = $this->get('/this-route-does-not-exist');
        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Oops! Page Not Found');
    }
}
