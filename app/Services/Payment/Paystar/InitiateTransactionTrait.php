<?php

namespace App\Services\Payment\Paystar;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait InitiateTransactionTrait
{
    public function initiateTransaction(Order $order, $cardNumber)
    {
        $user = auth()->user();
        $callbackUrl = route('payment.callback' , $this->gatewayName);
        $totalPrice = (new OrderRepository($order))->totalPrice();
        $secretKey = env('key_hash');
        $gatewayID = env('GATEWAY_ID');
        $sign = $this->generateSignatureForInitiateTransaction($totalPrice, $order->id, $callbackUrl, $secretKey);

        $paramsCreateApiPayStar = [
            "amount" => $totalPrice,
            "order_id" => $order->id,
            "callback" => $callbackUrl,
            "callback_method" => 1,
            "sign" => $sign,
            "name" => $user->name,
            "mail" => $user->email,
            "card_number" => $cardNumber,
        ];

        $headers = ['Authorization' => 'Bearer ' . $gatewayID];

        $response = $this->apiRequestService->post('api/pardakht/create', $paramsCreateApiPayStar, $headers);

        if ($response['status'] === 1) {
            return $this->createPayment($user, $order, $response['data'], $cardNumber, $totalPrice);
        } else {
            Log::error("Api Create Paystar Was Failed  - status : {$response['status']} - message: {$response['message']}  ");
            return false;
        }
    }
    private function generateSignatureForInitiateTransaction($amount, $order_id, $callback, $secretKey)
    {
        $data = "{$amount}#{$order_id}#{$callback}";
        return hash_hmac('sha512', $data, $secretKey);
    }
    private function createPayment($user, $order, $data, $cardNumber, $totalPrice)
    {
        $paymentRepo = new PaymentRepository(new Payment());
        $reservedMinutes = config('app.reserved_transaction_time_paystar') ?? 15;

        return $paymentRepo->create([
            "user_id" => $user->id,
            "order_id" => $order->id,
            "gateway_name" => $this->gatewayName,
            "ref_num" => $data['ref_num'],
            "card_number" => $cardNumber,
            "order_amount" => $totalPrice,
            "final_amount" => $data['payment_amount'],
            "token" => $data['token'],
            "is_paid" => false,
            "reserved_transaction" => Carbon::now()->addMinute($reservedMinutes)->toDateTimeString()
        ]);
    }
}
