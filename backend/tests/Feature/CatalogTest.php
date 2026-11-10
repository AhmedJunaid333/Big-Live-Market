<?php

namespace Tests\Feature;

use Database\Seeders\TaxonomyAndGeographySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TaxonomyAndGeographySeeder::class);
    }

    public function test_can_fetch_countries(): void
    {
        $response = $this->getJson('/api/v1/countries');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.iso2', 'PK')
            ->assertJsonPath('data.0.name', 'Pakistan');
    }

    public function test_can_fetch_regions_and_cities(): void
    {
        $regionsResponse = $this->getJson('/api/v1/regions');
        $regionsResponse->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $punjabId = $regionsResponse->json('data.0.id');

        $citiesResponse = $this->getJson('/api/v1/cities?region_id=' . $punjabId);
        $citiesResponse->assertStatus(200)
            ->assertJsonCount(2, 'data'); // Lahore, Rawalpindi
    }

    public function test_can_fetch_categories_tree_and_attributes(): void
    {
        $categoriesResponse = $this->getJson('/api/v1/categories');
        $categoriesResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'children']
                ]
            ]);

        $detailsResponse = $this->getJson('/api/v1/categories/cars');
        $detailsResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Cars')
            ->assertJsonStructure(['data' => ['attributes', 'brands']]);

        $carsCategoryId = $detailsResponse->json('data.id');

        $attributesResponse = $this->getJson('/api/v1/categories/' . $carsCategoryId . '/attributes');
        $attributesResponse->assertStatus(200)
            ->assertJsonPath('data.0.code', 'year')
            ->assertJsonPath('data.0.required', true);
    }

    public function test_can_fetch_brands_and_models(): void
    {
        $brandsResponse = $this->getJson('/api/v1/brands');
        $brandsResponse->assertStatus(200);

        $toyota = collect($brandsResponse->json('data'))->firstWhere('slug', 'toyota');
        $this->assertNotNull($toyota);

        $modelsResponse = $this->getJson('/api/v1/models?brand_id=' . $toyota['id']);
        $modelsResponse->assertStatus(200)
            ->assertJsonCount(2, 'data'); // Corolla, Yaris
    }
}
