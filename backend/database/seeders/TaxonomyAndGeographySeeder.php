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

        // 4. Categories
        $motors = Category::firstOrCreate(
            ['slug' => 'motors'],
            ['name' => 'Motors', 'icon' => 'directions_car', 'sort_order' => 1]
        );
        $cars = Category::firstOrCreate(
            ['slug' => 'cars'],
            ['parent_id' => $motors->id, 'name' => 'Cars', 'icon' => 'directions_car', 'sort_order' => 1]
        );

        $mobiles = Category::firstOrCreate(
            ['slug' => 'mobile-phones'],
            ['name' => 'Mobile Phones', 'icon' => 'smartphone', 'sort_order' => 2]
        );
        $smartphones = Category::firstOrCreate(
            ['slug' => 'smartphones'],
            ['parent_id' => $mobiles->id, 'name' => 'Smartphones', 'icon' => 'smartphone', 'sort_order' => 1]
        );

        $property = Category::firstOrCreate(
            ['slug' => 'property'],
            ['name' => 'Property', 'icon' => 'home', 'sort_order' => 3]
        );

        $electronics = Category::firstOrCreate(
            ['slug' => 'electronics-appliances'],
            ['name' => 'Electronics & Appliances', 'icon' => 'tv', 'sort_order' => 4]
        );

        // 5. Category Attributes for Cars
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

        // 6. Category Attributes for Smartphones
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

        // 7. Brands & Models for Cars
        $toyota = Brand::firstOrCreate(['slug' => 'toyota'], ['category_id' => $cars->id, 'name' => 'Toyota']);
        ProductModel::firstOrCreate(['brand_id' => $toyota->id, 'slug' => 'corolla'], ['name' => 'Corolla']);
        ProductModel::firstOrCreate(['brand_id' => $toyota->id, 'slug' => 'yaris'], ['name' => 'Yaris']);

        $honda = Brand::firstOrCreate(['slug' => 'honda'], ['category_id' => $cars->id, 'name' => 'Honda']);
        ProductModel::firstOrCreate(['brand_id' => $honda->id, 'slug' => 'civic'], ['name' => 'Civic']);
        ProductModel::firstOrCreate(['brand_id' => $honda->id, 'slug' => 'city'], ['name' => 'City']);

        // 8. Brands & Models for Smartphones
        $apple = Brand::firstOrCreate(['slug' => 'apple'], ['category_id' => $smartphones->id, 'name' => 'Apple']);
        ProductModel::firstOrCreate(['brand_id' => $apple->id, 'slug' => 'iphone-15-pro'], ['name' => 'iPhone 15 Pro']);
        ProductModel::firstOrCreate(['brand_id' => $apple->id, 'slug' => 'iphone-16-pro-max'], ['name' => 'iPhone 16 Pro Max']);

        $samsung = Brand::firstOrCreate(['slug' => 'samsung'], ['category_id' => $smartphones->id, 'name' => 'Samsung']);
        ProductModel::firstOrCreate(['brand_id' => $samsung->id, 'slug' => 'galaxy-s24-ultra'], ['name' => 'Galaxy S24 Ultra']);
    }
}
