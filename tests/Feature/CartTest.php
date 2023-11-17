<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->post(route('basket.add', ['product' => $product->id, 'quantity' => 2]));

        $response->assertRedirect(route('product.index'));
        $response->assertSessionHas('cart.'.$product->id);
    }

    public function test_can_remove_product_from_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $this->post(route('basket.add', ['product' => $product->id, 'quantity' => 1]));

        $response = $this->post(route('basket.remove', ['product' => $product->id]));

        $response->assertRedirect(route('basket.index'));
        $response->assertSessionMissing('cart.'.$product->id);
    }

    public function test_cannot_add_product_to_cart_if_inventory_insufficient()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['inventory' => 5]);

        $response = $this->post(route('basket.add', ['product' => $product->id, 'quantity' => 10]));

        $response->assertSessionHas('error');
    }
}
