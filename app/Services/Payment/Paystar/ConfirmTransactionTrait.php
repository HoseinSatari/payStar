<?php

namespace App\Services\Payment\Paystar;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Services\ApiRequest\ApiRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ConfirmTransactionTrait
{
    public function confirmTransaction(Request $request)
    {
        $responseData = $request->all();

        if (!$this->validateBankResponse($responseData)) {
            $this->unreservePayment($responseData);
            return $this->errorResponse('Your payment was unsuccessful!');
        }

        $orderRepo = $this->findOrder($responseData['order_id']);
        if (!$orderRepo) {
            return $this->errorResponse('Order not found! If the amount is deducted from your account, the amount will be returned within 72 hours');
        }

        $paymentRepo = $this->findPayment($orderRepo, $responseData['ref_num']);
        if (!$paymentRepo) {
            return $this->errorResponse('Payment not found! If the amount is deducted from your account, the amount will be returned within 72 hours');
        }

        $verifyStatus = $this->verifyTransactionPaystar($paymentRepo , $responseData['tracking_code']);

        if (!$verifyStatus) {
            $this->unreservePayment($responseData);
            return $this->errorResponse('There is a problem with the payment system, please try again later! Or contact the site administrator');
        }
        $this->updateOrderAndPayment($orderRepo, $paymentRepo, $responseData);
        return $this->successResponse('Your order has been paid! Thank you for trusting us.');
    }

    private function verifyTransactionPaystar($paymentRepo , $tracking_code)
    {
        $secretKey = env('key_hash');
        $gatewayID = env('GATEWAY_ID');
        $headers = ['Authorization' => 'Bearer ' . $gatewayID];
        $sign = $this->generateSignatureForVerifyTransaction($paymentRepo->final_amount, $paymentRepo->ref_num, $paymentRepo->card_number, $tracking_code, $secretKey);

        $paramsCreateApiPayStar = [
            "ref_num" => $paymentRepo->ref_num,
            "amount" => $paymentRepo->final_amount,
            "sign" => $sign,
        ];
        // TODO SIGN NOT VALID FROM PAYSTAR
        $response = $this->apiRequestService->post('api/pardakht/verify', $paramsCreateApiPayStar, $headers);

        if ($response['status'] == 1) {
            return true;
        } else {
            $response['message'] = $response['message'] ?? 'no message available';
            Log::error("Problem in Paystar transaction confirmation api  - message : {$response['message']}   ");
            return false;
        }

    }

    private function generateSignatureForVerifyTransaction($amount, $refNum, $cardNumber, $trackingCode, $secretKey)
    {
        $data = "{$amount}#{$refNum}#{$cardNumber}#{$trackingCode}";
        return hash_hmac('sha512', $data, $secretKey);
    }

    private function unreservePayment($responseData)
    {
        $orderRepo = $this->findOrder($responseData['order_id']);
        $paymentRepo = $this->findPayment($orderRepo, $responseData['ref_num']);
        $paymentRepo->update([
            'pay_time' => Carbon::now()->toDateTimeString(),
            'transaction_id' => null,
            'status_code_response_gateway' => $responseData['status'],
            'tracking_code' => null,
            'reserved_transaction' => null
        ]);
    }

    protected function validateBankResponse(array $responseData)
    {
        if ($responseData['status'] == 1) {
            return true;
        }

        Log::error("fail paid gateway callback paystar - status: {$responseData['status']} - ref_num: {$responseData['ref_num']}");
        return false;
    }

    protected function findOrder($orderId)
    {
        $orderRepo = new OrderRepository(new Order());
        $order = $orderRepo->find($orderId);
        if (!$order) {
            Log::error('order not found In confirmation Transaction Paystar');
            return false;
        }
        return $orderRepo;
    }

    protected function findPayment($order, $refNum)
    {
        $payment = $order->transactions()->where(['ref_num' => $refNum])->first();
        if (!$payment) {
            Log::error('payment not found In confirmation Transaction Paystar');
            return false;
        }
        $paymentRepo = new PaymentRepository($payment);
        return $paymentRepo;
    }

    protected function updateOrderAndPayment($order, $payment, $responseData)
    {
        $order->update(['status' => 'paid']);
        $payment->update([
            'is_paid' => true,
            'pay_time' => Carbon::now()->toDateTimeString(),
            'transaction_id' => $responseData['transaction_id'],
            'status_code_response_gateway' => $responseData['status'],
            'tracking_code' => $responseData['tracking_code'],
            'reserved_transaction' => null
        ]);
    }

    protected function successResponse($message)
    {
        return $this->getResponseArray('user.order.index', [], 'success', $message);
    }

    protected function errorResponse($message)
    {
        return $this->getResponseArray('user.order.index', [], 'error', $message);
    }

    protected function getResponseArray($route, $params, $type, $message)
    {
        return ['route' => $route, 'params' => $params, 'type' => $type, 'message' => $message];
    }
}
