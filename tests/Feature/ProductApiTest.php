<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_product_via_api(): void
    {
        $category = Category::create([
            'category' => 'Romansa',
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Dilan 1990',
            'description' => 'Novel romantis',
            'price' => 50000,
            'stock' => 10,
            'category_id' => $category->category_id,
            'img' => 'dilan.jpg',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'Dilan 1990')
            ->assertJsonPath('category_id', $category->category_id);
    }
}
