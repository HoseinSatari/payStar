<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_are_displayed_on_index()
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
    }
    public function test_can_create_product()
    {
        $this->actingAs(User::factory()->create());

        $productData = [
            'title' => 'Sample Product',
            'price' => '19.99',
            'inventory' => 100,
            'poster' => 'sample-poster.jpg',
        ];

        $productRepo = new ProductRepository(new Product());
        $product = $productRepo->create($productData);

        $this->assertDatabaseHas('products', ['title' => 'Sample Product']);
        $this->assertEquals('Sample Product', $product->title);
    }

    public function test_can_create_product_via_product_create_route()
    {
        $this->actingAs(User::factory()->create());

        $productData = [
            'title' => 'Sample Product',
            'price' => '19.99',
            'inventory' => 100,
            'poster' => 'sample-poster.jpg',
        ];

        $response = $this->post(route('product.create'), $productData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('products', ['title' => 'Sample Product']);
    }

    public function test_product_requires_title()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('product.create'), [
            'title' => '',
            'price' => '19.99',
            'inventory' => 100,
            'poster' => 'sample-poster.jpg',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_index_method_returns_view_with_products()
    {
        $this->actingAs(User::factory()->create());

        $products = Product::factory(10)->create();

        $response = $this->get(route('product.index'));

        $response->assertStatus(200);
        $response->assertViewIs('product.index');
        $response->assertViewHas('allProducts', $products);
    }

    public function test_product_requires_price()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('product.create'), [
            'title' => 'Sample Product',
            'price' => '',
            'inventory' => 100,
            'poster' => 'sample-poster.jpg',
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_product_requires_valid_price()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('product.create'), [
            'title' => 'Sample Product',
            'price' => 'invalid-price',
            'inventory' => 100,
            'poster' => 'sample-poster.jpg',
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_product_requires_inventory()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('product.create'), [
            'title' => 'Sample Product',
            'price' => '19.99',
            'inventory' => '',
            'poster' => 'sample-poster.jpg',
        ]);

        $response->assertSessionHasErrors('inventory');
    }

    public function test_product_requires_valid_inventory()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('product.create'), [
            'title' => 'Sample Product',
            'price' => '19.99',
            'inventory' => 'invalid-inventory',
            'poster' => 'sample-poster.jpg',
        ]);

        $response->assertSessionHasErrors('inventory');
    }

    public function test_product_can_have_null_poster()
    {
        $this->actingAs(User::factory()->create());

        $productData = [
            'title' => 'Sample Product',
            'price' => '19.99',
            'inventory' => 100,
            'poster' => null,
        ];

        $productRepo = new ProductRepository(new Product());
        $product = $productRepo->create($productData);

        $this->assertDatabaseHas('products', ['title' => 'Sample Product']);
        $this->assertNull($product->poster);
    }
}
