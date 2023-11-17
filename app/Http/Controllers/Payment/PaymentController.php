<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;

use App\Services\Payment\PaymentGatewayManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    use CreateTrait, RedirectToGatewayTrait, CallbackTrait;

    protected $orderRepo;
    protected $paymentRepo;
    protected $paymentGatewayManager;

    public function __construct(OrderRepository $orderRepo, PaymentRepository $paymentRepo, PaymentGatewayManager $paymentGatewayManager)
    {
        $this->orderRepo = $orderRepo;
        $this->paymentRepo = $paymentRepo;
        $this->paymentGatewayManager = $paymentGatewayManager;
    }

    private function handlePaymentExists()
    {
        if ($this->orderRepo->transactions(['is_paid' => true])->exists()) {
            Log::error('User Try Pay Order is already paid - order_id : ' . $this->orderRepo->id);
            return true;
        }

        return false;
    }

    private function handleExistingTransaction()
    {
        $transaction = $this->orderRepo->transactions()->where('reserved_transaction', '>', Carbon::now()->toDateTimeString())->latest()->first();

        if ($transaction) {
            $this->paymentRepo->findOrFail($transaction->id);
            Log::info('A transaction is trying to pay again - payment_id : ' . $this->paymentRepo->id);
            return true;
        }

        return false;
    }

    public function orderReservedIsActive()
    {
        $reservedMinutes = config('app.reserved_order_time') ?? 15;
        return $this->orderRepo->created_at > now()->setTimezone('Asia/Tehran')->subMinutes($reservedMinutes);
    }
}
