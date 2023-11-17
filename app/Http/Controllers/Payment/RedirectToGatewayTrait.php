<?php

namespace App\Http\Controllers\Payment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait RedirectToGatewayTrait
{
    private function handleDubbleTransactionExists($payment)
    {
        $transactionUnpaidReserved = $this->orderRepo->transactions()->where('reserved_transaction', '>', Carbon::now()->toDateTimeString())->latest()->first();

        if ($transactionUnpaidReserved && $transactionUnpaidReserved->id !== $payment->id) {
            Log::error('There is two open transactions for one order - idLastRow : ' . $transactionUnpaidReserved->id . ' - idRequestRow ' . $payment->id);
            return true;
        }
        return false;
    }

    private function handleActiveTransaction()
    {
        $transactionUnpaidReserved = $this->orderRepo->transactions()->where('reserved_transaction', '>', Carbon::now()->toDateTimeString())->latest()->first();

        if (!$transactionUnpaidReserved) {
            Log::error('There is a problem in the section directing the user to the port in the prepare method - there is no reservation transaction and the method has been executed so far this log - idRequestRow ' . $this->paymentRepo->id);
            return false;
        }
        return true;
    }

    public function prepare(Request $request)
    {
        if (!$order = $this->orderRepo->find($request->order)) {
            return abort(404);
        }
        if (!$payment = $this->paymentRepo->find($request->payment)) {
            return abort(404);
        }

        if ($this->handlePaymentExists()) {
            return redirect()->route('user.order.index')->with('error', 'This order has already been paid');
        }
        if (!$this->orderReservedIsActive()){
            return redirect()->route('user.order.index')->with('error', 'Your order has expired and cannot be paid');
        }
        if (!$this->handleActiveTransaction()) {
            return redirect()->route('payment.create', $order->id)->with('error', 'Your transaction has timed out, please try again');
        }
        if ($resultTwoTransactionExists = $this->handleDubbleTransactionExists($payment)) {
            return redirect()->route('payment.create', $order->id)->with('error', 'There is a problem in the system! Please try again or contact the site administrator.');
        }
        $products = $this->orderRepo->products();
        $reserved_time = $this->paymentRepo->left_over_time_reserved();

        return view('payment.prepared', compact('products', 'order', 'payment', 'reserved_time'));
    }

    public function redirectToGateway(Request $request)
    {
        if (!$order = $this->orderRepo->find($request->order)) {
            return abort(404);
        }
        if (!$payment = $this->paymentRepo->find($request->payment)) {
            return abort(404);
        }
        if (!$this->orderReservedIsActive()){
            return redirect()->route('user.order.index')->with('error', 'Your order has expired and cannot be paid');
        }
        if ($this->handlePaymentExists()) {
            return redirect()->route('user.order.index')->with('error', 'This order has already been paid');
        }
        if (!$this->handleActiveTransaction()) {
            return redirect()->route('payment.create', $order->id)->with('error', 'Your transaction has timed out, please try again');
        }
        if ($this->handleDubbleTransactionExists($payment)) {
            return redirect()->route('payment.create', $order->id)->with('error', 'There is a problem in the system! Please try again or contact the site administrator.');
        }
        $paymentGateway = $this->paymentGatewayManager->getGateway($this->paymentRepo->gateway_name);
        $redirectUrl = $paymentGateway->getUrlRedirect($payment);

        return redirect($redirectUrl);
    }

}
