<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            TaxonomyAndGeographySeeder::class,
            MonetizationSeeder::class,
        ]);

        // 1. Super Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@biglive.pk'],
            [
                'name' => 'Big Live Administrator',
                'username' => 'admin',
                'password' => Hash::make('Admin@12345'),
                'phone_e164' => '+923000000001',
                'status' => 'active',
                'locale' => 'en',
                'preferred_currency' => 'PKR',
            ]
        );
        $admin->assignRole('super_admin');

        // 2. Verified Seller Demo Account
        $seller = User::firstOrCreate(
            ['email' => 'seller@biglive.pk'],
            [
                'name' => 'Hamza Motors & Electronics',
                'username' => 'hamza_seller',
                'password' => Hash::make('Seller@12345'),
                'phone_e164' => '+923001234567',
                'status' => 'active',
                'locale' => 'en',
                'preferred_currency' => 'PKR',
            ]
        );
        $seller->assignRole('verified_seller');

        // 3. Demo Buyer Account
        $buyer = User::firstOrCreate(
            ['email' => 'buyer@biglive.pk'],
            [
                'name' => 'Ali Khan Buyer',
                'username' => 'ali_buyer',
                'password' => Hash::make('Buyer@12345'),
                'phone_e164' => '+923009876543',
                'status' => 'active',
                'locale' => 'ur',
                'preferred_currency' => 'PKR',
            ]
        );
        $buyer->assignRole('buyer');

        // 4. Sample Active Listings for Web View
        $cars = Category::where('slug', 'cars')->first() ?? Category::first();
        $smartphones = Category::where('slug', 'smartphones')->first() ?? Category::first();
        $lahore = City::where('slug', 'lahore')->first() ?? City::first();
        $karachi = City::where('slug', 'karachi')->first() ?? City::first();
        $islamabad = City::where('slug', 'islamabad')->first() ?? City::first();

        Listing::firstOrCreate(
            ['title' => 'Toyota Corolla Altis Grande 1.8 2023'],
            [
                'seller_id' => $seller->id,
                'category_id' => $cars->id,
                'city_id' => $lahore->id,
                'description' => 'First owner, bumper to bumper genuine, sunroof, beige interior, Lahore registered with all token taxes paid.',
                'price_minor' => 745000000, // 7,450,000 PKR
                'currency' => 'PKR',
                'condition_code' => 'used',
                'status' => 'active',
                'moderation_status' => 'approved',
                'location_label' => 'DHA Phase 5, Lahore',
                'latitude' => 31.4704,
                'longitude' => 74.4108,
                'published_at' => now(),
            ]
        );

        Listing::firstOrCreate(
            ['title' => 'iPhone 15 Pro Max 256GB Natural Titanium'],
            [
                'seller_id' => $seller->id,
                'category_id' => $smartphones->id,
                'city_id' => $karachi->id,
                'description' => 'Official PTA approved with box and original cable. 98% battery health, completely scratchless condition.',
                'price_minor' => 43500000, // 435,000 PKR
                'currency' => 'PKR',
                'condition_code' => 'used',
                'status' => 'active',
                'moderation_status' => 'approved',
                'location_label' => 'Clifton, Karachi',
                'latitude' => 24.8138,
                'longitude' => 67.0300,
                'published_at' => now(),
            ]
        );

        Listing::firstOrCreate(
            ['title' => 'Honda Civic RS Turbo 2022 Black Edition'],
            [
                'seller_id' => $seller->id,
                'category_id' => $cars->id,
                'city_id' => $islamabad->id,
                'description' => 'Top of the line RS Turbo, 24,000 km driven only, Islamabad registered, brand new Michelin tires.',
                'price_minor' => 880000000, // 8,800,000 PKR
                'currency' => 'PKR',
                'condition_code' => 'used',
                'status' => 'active',
                'moderation_status' => 'approved',
                'location_label' => 'F-7 Markaz, Islamabad',
                'latitude' => 33.7215,
                'longitude' => 73.0560,
                'published_at' => now(),
            ]
        );

        Listing::firstOrCreate(
            ['title' => 'Samsung Galaxy S24 Ultra 512GB Titanium Gray'],
            [
                'seller_id' => $seller->id,
                'category_id' => $smartphones->id,
                'city_id' => $lahore->id,
                'description' => 'Brand new sealed pack, official Samsung Pakistan 1 year warranty with receipt.',
                'price_minor' => 39500000, // 395,000 PKR
                'currency' => 'PKR',
                'condition_code' => 'new',
                'status' => 'active',
                'moderation_status' => 'approved',
                'location_label' => 'Gulberg III, Lahore',
                'latitude' => 31.5100,
                'longitude' => 74.3440,
                'published_at' => now(),
            ]
        );
    }
}
