<?php

namespace App\Services\Payment\Paystar;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Log;

trait RedirectToGatewayTrait
{
    public function getUrlRedirect(Payment $payment)
    {
        $paymentRepo = new PaymentRepository($payment);

        if (!$paymentRepo->token) {
            Log::error('Attempting to send to the port for a transaction without a token - payment_id : ' . $paymentRepo->id);
            return false;
        }

        $url = "https://core.paystar.ir/api/pardakht/payment" . '?token=' . $paymentRepo->token;

        return $url;
    }
}
