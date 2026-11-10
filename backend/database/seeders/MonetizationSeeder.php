<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PromotionProduct;
use Illuminate\Database\Seeder;

class MonetizationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Subscription Plans
        Plan::firstOrCreate(
            ['code' => 'starter'],
            [
                'name' => 'Starter Dealer',
                'currency' => 'PKR',
                'price_minor' => 500000, // 5,000 PKR
                'billing_period' => 'monthly',
                'listing_quota' => 50,
                'benefits_json' => ['50 Active Listings', 'Basic Analytics', 'Storefront Profile'],
                'active' => true,
            ]
        );

        Plan::firstOrCreate(
            ['code' => 'pro_dealer'],
            [
                'name' => 'Pro Business',
                'currency' => 'PKR',
                'price_minor' => 2000000, // 20,000 PKR
                'billing_period' => 'monthly',
                'listing_quota' => 250,
                'benefits_json' => ['250 Active Listings', 'Verified Business Badge', 'Up to 5 Team Members', 'Priority Support'],
                'active' => true,
            ]
        );

        Plan::firstOrCreate(
            ['code' => 'enterprise'],
            [
                'name' => 'Enterprise Storefront',
                'currency' => 'PKR',
                'price_minor' => 6000000, // 60,000 PKR
                'billing_period' => 'monthly',
                'listing_quota' => 1000,
                'benefits_json' => ['1,000 Active Listings', 'Unlimited Team Members', 'Dedicated Account Manager', 'Bulk Inventory API'],
                'active' => true,
            ]
        );

        // 2. Promotion Products
        PromotionProduct::firstOrCreate(
            ['code' => 'featured_7d'],
            [
                'name' => 'Featured Listing (7 Days)',
                'placement' => 'featured',
                'duration_hours' => 168,
                'currency' => 'PKR',
                'price_minor' => 250000, // 2,500 PKR
                'active' => true,
            ]
        );

        PromotionProduct::firstOrCreate(
            ['code' => 'bump_3d'],
            [
                'name' => 'Daily Top Bump (3 Days)',
                'placement' => 'category_bump',
                'duration_hours' => 72,
                'currency' => 'PKR',
                'price_minor' => 120000, // 1,200 PKR
                'active' => true,
            ]
        );

        PromotionProduct::firstOrCreate(
            ['code' => 'top_spot_14d'],
            [
                'name' => 'Top Search Spot (14 Days)',
                'placement' => 'home_page',
                'duration_hours' => 336,
                'currency' => 'PKR',
                'price_minor' => 500000, // 5,000 PKR
                'active' => true,
            ]
        );
    }
}
