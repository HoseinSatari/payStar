<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cartService = new CartService();
        $orderRepo = new OrderRepository(new Order());
        $CartItems = $cartService->getCartItems();
        $totalPrice = $cartService->getTotalPrice();
        $orderProducts = $this->prepareOrderProducts($CartItems);

        $order = $orderRepo->create(['user_id' => auth()->id()]);
        $orderRepo->attachProducts($orderProducts);

//        $cartService->clearCart();
        return redirect()->route('payment.create', $order->id)->with('success', 'Order placed successfully!');
    }

    public function prepareOrderProducts($CartItems)
    {
        if ($CartItems == []) {
            Log::error('Basket Order Is Empty When Order Going To Save To Database');
            return redirect()->back()->with('error', 'Your Basket Is Empty Please Try Again');
        }
        return collect($CartItems)->mapWithKeys(function ($item) {
            return [$item['product']->id => ['quantity' => $item['quantity'], 'amount' => $item['product']->price]];
        })->toArray();
    }
}
