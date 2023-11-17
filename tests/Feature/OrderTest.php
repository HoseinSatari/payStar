<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    public function test_store_method_creates_order_and_redirects_to_payment_create_route()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'title' => 'product test',
            'inventory' => 30,
            'price' => 100
        ]);

        $cartService = new CartService();
        $cartService->addToCart($product, 2);


        $response = $this->post('/order/store');


        $response->assertStatus(302);

        $order = Order::first();
        $this->assertNotNull($order);

        $this->assertEquals(auth()->id(), $order->user_id);

        $this->assertCount(1, $order->products);
        $this->assertEquals($product->id, $order->products->first()->id);

        $response->assertRedirect(route('payment.create', $order->id));
    }
}
