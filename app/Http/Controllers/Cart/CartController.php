<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Payment\PaymentGatewayManager;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    public function addToCart(Product $product, $quantity = 1)
    {
        try {
            $this->cartService->addToCart($product, $quantity);
            return redirect()->route('product.index')->with('success', 'Product added to cart successfully!');
        } catch (\Exception $e) {
            return redirect()->route('product.index')->with('error', $e->getMessage());
        }
    }

    public function removeFromCart(Product $product)
    {
        $this->cartService->removeFromCart($product);
        return redirect()->route('basket.index')->with('success', 'Product remove from cart successfully!');
    }

    public function viewCart()
    {
        if ($cartItems = $this->cartService->getCartItems()) {

            $totalPrice = $this->cartService->getTotalPrice();

            return view('basket.index', compact('cartItems', 'totalPrice'));
        }

        return redirect()->route('product.index')->with('error', 'Your basket is empty');
    }
}
