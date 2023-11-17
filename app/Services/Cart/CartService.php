<?php

namespace App\Services\Cart;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $cartKey = 'cart';

    public function addToCart(Product $product, $quantity = 1)
    {
        $this->checkProductInventory($product, $quantity);

        $cart = Session::get($this->cartKey, []);

        $productId = $product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        Session::put($this->cartKey, $cart);
    }
    public function clearCart()
    {
        Session::forget($this->cartKey);
    }
    public function removeFromCart(Product $product)
    {
        $cart = Session::get($this->cartKey, []);

        $productId = $product->id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        Session::put($this->cartKey, $cart);
    }

    public function getCartItems()
    {
        return Session::get($this->cartKey, []);
    }

    protected function checkProductInventory(Product $product, $quantity)
    {
        if ($product->inventory < $quantity) {
            throw new \Exception("Insufficient inventory for product: {$product->title}");
        }
    }
    public function getTotalPrice()
    {
        $cart = Session::get($this->cartKey, []);

        $totalPrice = 0;

        foreach ($cart as $cartItem) {
            $product = $cartItem['product'];
            $quantity = $cartItem['quantity'];

            $totalPrice += $product->price * $quantity;
        }

        return $totalPrice;
    }
}
