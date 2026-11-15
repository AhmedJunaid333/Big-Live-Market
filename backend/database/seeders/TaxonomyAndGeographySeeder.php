<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Country;
use App\Models\ProductModel;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomyAndGeographySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Countries
        $pk = Country::firstOrCreate(
            ['iso2' => 'PK'],
            ['name' => 'Pakistan', 'default_currency' => 'PKR', 'enabled' => true]
        );

        // 2. Regions
        $punjab = Region::firstOrCreate(
            ['country_id' => $pk->id, 'name' => 'Punjab'],
            ['code' => 'PB']
        );
        $sindh = Region::firstOrCreate(
            ['country_id' => $pk->id, 'name' => 'Sindh'],
            ['code' => 'SD']
        );
        $ict = Region::firstOrCreate(
            ['country_id' => $pk->id, 'name' => 'Islamabad Capital Territory'],
            ['code' => 'ICT']
        );

        // 3. Cities
        City::firstOrCreate(
            ['region_id' => $punjab->id, 'slug' => 'lahore'],
            ['name' => 'Lahore', 'latitude' => 31.5204, 'longitude' => 74.3587]
        );
        City::firstOrCreate(
            ['region_id' => $punjab->id, 'slug' => 'rawalpindi'],
            ['name' => 'Rawalpindi', 'latitude' => 33.5651, 'longitude' => 73.0169]
        );
        City::firstOrCreate(
            ['region_id' => $sindh->id, 'slug' => 'karachi'],
            ['name' => 'Karachi', 'latitude' => 24.8607, 'longitude' => 67.0011]
        );
        City::firstOrCreate(
            ['region_id' => $ict->id, 'slug' => 'islamabad'],
            ['name' => 'Islamabad', 'latitude' => 33.6844, 'longitude' => 73.0479]
        );

        // 4. Main Categories & Comprehensive Subcategories
        $taxonomy = [
            // MOBILES
            [
                'id' => 3,
                'slug' => 'mobile-phones',
                'name' => 'Mobile Phones',
                'icon' => 'smartphone',
                'sort_order' => 1,
                'children' => [
                    ['slug' => 'smartphones', 'name' => 'Mobile Phones', 'icon' => 'smartphone'],
                    ['slug' => 'mobile-accessories', 'name' => 'Mobile Accessories', 'icon' => 'headphones'],
                    ['slug' => 'tablets', 'name' => 'Tablets', 'icon' => 'tablet'],
                    ['slug' => 'smart-watches', 'name' => 'Smart Watches', 'icon' => 'watch'],
                    ['slug' => 'landline-wireless-phones', 'name' => 'Landline & Wireless Phones', 'icon' => 'phone'],
                    ['slug' => 'other-wearables-audio', 'name' => 'Other Wearables & Audio', 'icon' => 'headset'],
                    ['slug' => 'mobile-numbers', 'name' => 'Mobile Numbers & VIP SIMs', 'icon' => 'sim_card'],
                ]
            ],
            // VEHICLES
            [
                'id' => 1,
                'slug' => 'motors',
                'name' => 'Vehicles',
                'icon' => 'directions_car',
                'sort_order' => 2,
                'children' => [
                    ['slug' => 'cars', 'name' => 'Cars', 'icon' => 'directions_car'],
                    ['slug' => 'car-accessories', 'name' => 'Car Accessories', 'icon' => 'speaker'],
                    ['slug' => 'spare-parts', 'name' => 'Spare Parts & Components', 'icon' => 'build'],
                    ['slug' => 'car-care', 'name' => 'Car Care & Detailing', 'icon' => 'cleaning_services'],
                    ['slug' => 'rickshaw-chingchi', 'name' => 'Rickshaw & Chingchi', 'icon' => 'electric_rickshaw'],
                    ['slug' => 'buses-vans-trucks', 'name' => 'Buses, Vans & Trucks', 'icon' => 'airport_shuttle'],
                    ['slug' => 'tractors-trailers', 'name' => 'Tractors & Trailers', 'icon' => 'agriculture'],
                    ['slug' => 'oil-lubricants', 'name' => 'Oil & Lubricants', 'icon' => 'water_drop'],
                    ['slug' => 'cars-on-installments', 'name' => 'Cars on Installments', 'icon' => 'payments'],
                    ['slug' => 'boats-watercraft', 'name' => 'Boats & Watercraft', 'icon' => 'sailing'],
                    ['slug' => 'other-vehicles', 'name' => 'Other Vehicles', 'icon' => 'commute'],
                ]
            ],
            // PROPERTY FOR SALE
            [
                'id' => 5,
                'slug' => 'property-for-sale',
                'name' => 'Property for Sale',
                'icon' => 'holiday_village',
                'sort_order' => 3,
                'children' => [
                    ['slug' => 'houses-for-sale', 'name' => 'Houses & Luxury Villas', 'icon' => 'home'],
                    ['slug' => 'residential-plots', 'name' => 'Residential Plots & Files', 'icon' => 'landscape'],
                    ['slug' => 'apartments-flats-sale', 'name' => 'Apartments & Penthouses', 'icon' => 'apartment'],
                    ['slug' => 'commercial-plots-shops', 'name' => 'Commercial Plots & Shops', 'icon' => 'storefront'],
                    ['slug' => 'offices-corporate-spaces', 'name' => 'Offices & Corporate Floors', 'icon' => 'business'],
                    ['slug' => 'agricultural-land-farmhouses', 'name' => 'Agricultural Land & Farm Houses', 'icon' => 'terrain'],
                ]
            ],
            // PROPERTY FOR RENT
            [
                'id' => 12,
                'slug' => 'property-for-rent',
                'name' => 'Property for Rent',
                'icon' => 'vpn_key',
                'sort_order' => 4,
                'children' => [
                    ['slug' => 'houses-for-rent', 'name' => 'Houses for Rent', 'icon' => 'home_work'],
                    ['slug' => 'portions-floors-rent', 'name' => 'Portions & Floors', 'icon' => 'layers'],
                    ['slug' => 'apartments-flats-rent', 'name' => 'Apartments & Flats for Rent', 'icon' => 'domain'],
                    ['slug' => 'rooms-flatmates-hostels', 'name' => 'Rooms, Flatmates & Hostels', 'icon' => 'meeting_room'],
                    ['slug' => 'commercial-shops-rent', 'name' => 'Commercial Shops for Rent', 'icon' => 'shopping_bag'],
                    ['slug' => 'commercial-offices-rent', 'name' => 'Commercial Offices for Rent', 'icon' => 'chair_alt'],
                    ['slug' => 'warehouses-industrial-sheds', 'name' => 'Warehouses & Industrial Sheds', 'icon' => 'warehouse'],
                    ['slug' => 'vacation-rentals-guest-houses', 'name' => 'Vacation Rentals & Guest Houses', 'icon' => 'hotel'],
                ]
            ],
            // ELECTRONICS & HOME APPLIANCES
            [
                'id' => 6,
                'slug' => 'electronics-appliances',
                'name' => 'Electronics & Home Appliances',
                'icon' => 'tv',
                'sort_order' => 5,
                'children' => [
                    ['slug' => 'tv-video-audio', 'name' => 'TV, Video & Audio', 'icon' => 'tv'],
                    ['slug' => 'air-conditioners-coolers', 'name' => 'Air Conditioners & Coolers', 'icon' => 'ac_unit'],
                    ['slug' => 'refrigerators-freezers', 'name' => 'Refrigerators & Freezers', 'icon' => 'kitchen'],
                    ['slug' => 'washing-machines-dryers', 'name' => 'Washing Machines & Dryers', 'icon' => 'local_laundry_service'],
                    ['slug' => 'kitchen-appliances', 'name' => 'Kitchen Appliances', 'icon' => 'microwave'],
                    ['slug' => 'generators-ups-solar', 'name' => 'Generators, UPS & Solar', 'icon' => 'bolt'],
                    ['slug' => 'cameras-photography-gear', 'name' => 'Cameras & Photography Gear', 'icon' => 'photo_camera'],
                    ['slug' => 'computers-laptops-accessories', 'name' => 'Computers, Laptops & Accessories', 'icon' => 'laptop'],
                    ['slug' => 'water-dispensers-geysers', 'name' => 'Water Dispensers & Geysers', 'icon' => 'water_damage'],
                ]
            ],
            // SERVICES
            [
                'id' => 7,
                'slug' => 'services',
                'name' => 'Services',
                'icon' => 'format_paint',
                'sort_order' => 6,
                'children' => [
                    ['slug' => 'home-repair-maintenance', 'name' => 'Home Repair & Maintenance', 'icon' => 'plumbing'],
                    ['slug' => 'drivers-car-rentals', 'name' => 'Drivers & Vehicle Rentals', 'icon' => 'time_to_leave'],
                    ['slug' => 'movers-packers', 'name' => 'Movers & Packers', 'icon' => 'local_shipping'],
                    ['slug' => 'ac-repair-servicing', 'name' => 'AC Repair & Servicing', 'icon' => 'hvac'],
                    ['slug' => 'painting-renovation', 'name' => 'Painting & Renovation', 'icon' => 'brush'],
                    ['slug' => 'event-wedding-services', 'name' => 'Event & Wedding Services', 'icon' => 'celebration'],
                    ['slug' => 'web-it-software-development', 'name' => 'Web, IT & Software Development', 'icon' => 'code'],
                    ['slug' => 'tuition-academics-coaching', 'name' => 'Tuition & Academic Coaching', 'icon' => 'school'],
                    ['slug' => 'travel-visa-tours', 'name' => 'Travel, Visa & Tours', 'icon' => 'flight_takeoff'],
                ]
            ],
            // JOBS
            [
                'id' => 8,
                'slug' => 'jobs',
                'name' => 'Jobs',
                'icon' => 'business_center',
                'sort_order' => 7,
                'children' => [
                    ['slug' => 'sales-marketing-jobs', 'name' => 'Sales & Marketing', 'icon' => 'trending_up'],
                    ['slug' => 'it-software-jobs', 'name' => 'IT & Software Development', 'icon' => 'terminal'],
                    ['slug' => 'customer-support-call-center', 'name' => 'Customer Support & Call Centers', 'icon' => 'support_agent'],
                    ['slug' => 'delivery-riders-drivers', 'name' => 'Delivery Riders & Drivers', 'icon' => 'delivery_dining'],
                    ['slug' => 'accounts-finance-banking', 'name' => 'Accounts, Finance & Banking', 'icon' => 'account_balance_wallet'],
                    ['slug' => 'office-admin-reception', 'name' => 'Office Admin & Receptionists', 'icon' => 'badge'],
                    ['slug' => 'education-teaching-jobs', 'name' => 'Education & Teaching', 'icon' => 'menu_book'],
                    ['slug' => 'medical-healthcare-jobs', 'name' => 'Medical & Healthcare', 'icon' => 'medical_services'],
                ]
            ],
            // ANIMALS
            [
                'id' => 9,
                'slug' => 'animals',
                'name' => 'Animals',
                'icon' => 'pets',
                'sort_order' => 8,
                'children' => [
                    ['slug' => 'birds', 'name' => 'Birds', 'icon' => 'flutter_dash'],
                    ['slug' => 'cats-kittens', 'name' => 'Cats & Kittens', 'icon' => 'cruelty_free'],
                    ['slug' => 'dogs-puppies', 'name' => 'Dogs & Puppies', 'icon' => 'pets'],
                    ['slug' => 'livestock-cattle', 'name' => 'Livestock & Cattle', 'icon' => 'grass'],
                    ['slug' => 'horses', 'name' => 'Horses', 'icon' => 'sports_kabaddi'],
                    ['slug' => 'fish-aquariums', 'name' => 'Fish & Aquariums', 'icon' => 'water'],
                    ['slug' => 'pet-food-accessories', 'name' => 'Pet Food & Accessories', 'icon' => 'shopping_basket'],
                ]
            ],
            // FURNITURE
            [
                'id' => 10,
                'slug' => 'furniture-home-decor',
                'name' => 'Furniture & Home Decor',
                'icon' => 'chair',
                'sort_order' => 9,
                'children' => [
                    ['slug' => 'sofas-couches-recliners', 'name' => 'Sofas, Couches & Recliners', 'icon' => 'weekend'],
                    ['slug' => 'beds-wardrobes', 'name' => 'Beds & Wardrobes', 'icon' => 'bed'],
                    ['slug' => 'dining-tables-chairs', 'name' => 'Dining Tables & Chairs', 'icon' => 'table_restaurant'],
                    ['slug' => 'office-furniture', 'name' => 'Office Furniture', 'icon' => 'chair_alt'],
                    ['slug' => 'curtains-carpets-rugs', 'name' => 'Curtains, Carpets & Rugs', 'icon' => 'texture'],
                    ['slug' => 'home-decor-lighting', 'name' => 'Home Decor & Lighting', 'icon' => 'lightbulb'],
                    ['slug' => 'garden-outdoor-furniture', 'name' => 'Garden & Outdoor Furniture', 'icon' => 'deck'],
                ]
            ],
            // FASHION & BEAUTY
            [
                'id' => 11,
                'slug' => 'fashion-beauty',
                'name' => 'Fashion & Beauty',
                'icon' => 'watch',
                'sort_order' => 10,
                'children' => [
                    ['slug' => 'mens-clothing', 'name' => "Men's Clothing", 'icon' => 'checkroom'],
                    ['slug' => 'womens-clothing', 'name' => "Women's Clothing", 'icon' => 'dry_cleaning'],
                    ['slug' => 'watches', 'name' => 'Watches (Luxury & Everyday)', 'icon' => 'watch_later'],
                    ['slug' => 'footwear-shoes', 'name' => 'Footwear & Shoes', 'icon' => 'roller_skating'],
                    ['slug' => 'jewelry-precious-stones', 'name' => 'Jewelry & Precious Stones', 'icon' => 'diamond'],
                    ['slug' => 'cosmetics-fragrances', 'name' => 'Cosmetics & Fragrances', 'icon' => 'auto_awesome'],
                    ['slug' => 'handbags-wallets', 'name' => 'Bags, Handbags & Wallets', 'icon' => 'shopping_bag'],
                ]
            ],
            // BIKES & MOTORCYCLES
            [
                'id' => 2,
                'slug' => 'bikes-motorcycles',
                'name' => 'Bikes & Motorcycles',
                'icon' => 'two_wheeler',
                'sort_order' => 11,
                'children' => [
                    ['slug' => 'motorcycles-standard', 'name' => 'Motorcycles & Scooters', 'icon' => 'two_wheeler'],
                    ['slug' => 'heavy-bikes-sports', 'name' => 'Heavy Bikes & Sports', 'icon' => 'sports_motorsports'],
                    ['slug' => 'electric-bikes', 'name' => 'Electric Bikes & Scooty', 'icon' => 'electric_scooter'],
                    ['slug' => 'bicycles', 'name' => 'Bicycles & Mountain Bikes', 'icon' => 'pedal_bike'],
                    ['slug' => 'bike-spare-parts-helmets', 'name' => 'Spare Parts, Helmets & Gear', 'icon' => 'security'],
                ]
            ],
        ];

        foreach ($taxonomy as $catData) {
            $parent = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'icon' => $catData['icon'],
                    'sort_order' => $catData['sort_order'],
                ]
            );

            if (isset($catData['children'])) {
                $subOrder = 1;
                foreach ($catData['children'] as $child) {
                    Category::firstOrCreate(
                        ['slug' => $child['slug']],
                        [
                            'parent_id' => $parent->id,
                            'name' => $child['name'],
                            'icon' => $child['icon'],
                            'sort_order' => $subOrder++,
                        ]
                    );
                }
            }
        }

        // 5. Category Attributes for Cars
        $cars = Category::where('slug', 'cars')->first();
        if ($cars) {
            CategoryAttribute::firstOrCreate(
                ['category_id' => $cars->id, 'code' => 'year'],
                [
                    'label' => 'Year',
                    'data_type' => 'number',
                    'required' => true,
                    'filterable' => true,
                    'validation_json' => ['min' => 1970, 'max' => 2027],
                    'sort_order' => 1,
                ]
            );
            CategoryAttribute::firstOrCreate(
                ['category_id' => $cars->id, 'code' => 'mileage_km'],
                [
                    'label' => 'Mileage (KM)',
                    'data_type' => 'number',
                    'required' => true,
                    'filterable' => true,
                    'sort_order' => 2,
                ]
            );
            CategoryAttribute::firstOrCreate(
                ['category_id' => $cars->id, 'code' => 'transmission'],
                [
                    'label' => 'Transmission',
                    'data_type' => 'select',
                    'required' => true,
                    'filterable' => true,
                    'options_json' => ['Automatic', 'Manual'],
                    'sort_order' => 3,
                ]
            );

            // Brands & Models for Cars
            $toyota = Brand::firstOrCreate(['slug' => 'toyota'], ['category_id' => $cars->id, 'name' => 'Toyota']);
            ProductModel::firstOrCreate(['brand_id' => $toyota->id, 'slug' => 'corolla'], ['name' => 'Corolla']);
            ProductModel::firstOrCreate(['brand_id' => $toyota->id, 'slug' => 'yaris'], ['name' => 'Yaris']);

            $honda = Brand::firstOrCreate(['slug' => 'honda'], ['category_id' => $cars->id, 'name' => 'Honda']);
            ProductModel::firstOrCreate(['brand_id' => $honda->id, 'slug' => 'civic'], ['name' => 'Civic']);
            ProductModel::firstOrCreate(['brand_id' => $honda->id, 'slug' => 'city'], ['name' => 'City']);
        }

        // 6. Category Attributes for Smartphones
        $smartphones = Category::where('slug', 'smartphones')->first();
        if ($smartphones) {
            CategoryAttribute::firstOrCreate(
                ['category_id' => $smartphones->id, 'code' => 'storage_gb'],
                [
                    'label' => 'Storage',
                    'data_type' => 'select',
                    'required' => true,
                    'filterable' => true,
                    'options_json' => ['64 GB', '128 GB', '256 GB', '512 GB', '1 TB'],
                    'sort_order' => 1,
                ]
            );

            // Brands & Models for Smartphones
            $apple = Brand::firstOrCreate(['slug' => 'apple'], ['category_id' => $smartphones->id, 'name' => 'Apple']);
            ProductModel::firstOrCreate(['brand_id' => $apple->id, 'slug' => 'iphone-15-pro'], ['name' => 'iPhone 15 Pro']);
            ProductModel::firstOrCreate(['brand_id' => $apple->id, 'slug' => 'iphone-16-pro-max'], ['name' => 'iPhone 16 Pro Max']);

            $samsung = Brand::firstOrCreate(['slug' => 'samsung'], ['category_id' => $smartphones->id, 'name' => 'Samsung']);
            ProductModel::firstOrCreate(['brand_id' => $samsung->id, 'slug' => 'galaxy-s24-ultra'], ['name' => 'Galaxy S24 Ultra']);
        }
    }
}
