<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function initiateTransaction(Order $order , $cardNumber);

    public function getUrlRedirect(Payment $payment);

    public function confirmTransaction(Request $request);
}
