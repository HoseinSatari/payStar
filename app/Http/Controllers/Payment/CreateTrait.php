<?php

namespace App\Http\Controllers\Payment;

use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

trait CreateTrait
{
    public function create(Request $request)
    {

        if (!$order = $this->orderRepo->find($request->order)) {
            return abort(404);
        }
        if (!$this->orderReservedIsActive()){
            return redirect()->route('user.order.index')->with('error', 'Your order has expired and cannot be paid');
        }
        if ($this->handlePaymentExists()) {
            return redirect()->route('user.order.index')->with('error', 'This order has already been paid');
        }
        if ($this->handleExistingTransaction()) {
            return redirect()->route('payment.prepare' , ['order' => $order->id , 'payment' => $this->paymentRepo->id])->with('success', "There is an open transaction for your order, the remaining time of the transaction is: {$this->paymentRepo->left_over_time_reserved()} minutes");
        }

        $products = $this->orderRepo->products();
        $allGateway = array_keys($this->paymentGatewayManager->getAllGateways());
        $totalPrice = $this->orderRepo->totalPrice();

        return view('payment.transaction', compact('allGateway', 'products', 'totalPrice', 'order'));
    }

    public function store(Request $request)
    {
        if (!$order = $this->orderRepo->find($request->order)) {
            return abort(404);
        }
        $validData = $request->validate([
            'card_number' => ['required', 'ir_bank_card_number'],
            'payment_gateway' => ['required', Rule::in(array_keys($this->paymentGatewayManager->getAllGateways()))],
        ]);
        if (!$this->orderReservedIsActive()){
            return redirect()->route('user.order.index')->with('error', 'Your order has expired and cannot be paid');
        }
        if ($this->handlePaymentExists()) {
            return redirect()->route('user.order.index')->with('error', 'This order has already been paid');
        }
        if ($this->handleExistingTransaction()) {
            return redirect()->route('payment.prepare' , ['order' => $this->orderRepo->id , 'payment' => $this->paymentRepo->id])->with('success', "There is an open transaction for your order, the remaining time of the transaction is: {$this->paymentRepo->left_over_time_reserved()} minutes");
        }

        $paymentGateway = $this->paymentGatewayManager->getGateway($validData['payment_gateway']);
        $transaction = $paymentGateway->initiateTransaction($order, $validData['card_number']);

        if (!$transaction) {
            return redirect()->route('payment.create', $this->orderRepo->id)->with('error', 'There is a problem in the system! Please try again or contact the site administrator.');
        }
        $paymentRepo = new PaymentRepository($transaction);
        return redirect()->route('payment.prepare'  , ['order' => $this->orderRepo->id , 'payment' => $paymentRepo->id])->with('success', "Your transaction is ready! Be careful, you only have {$paymentRepo->left_over_time_reserved()} minutes to pay");
    }
}
