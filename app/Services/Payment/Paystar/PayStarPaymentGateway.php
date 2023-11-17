<?php

namespace App\Services\Payment\Paystar;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Services\ApiRequest\ApiRequestService;
use App\Services\Payment\PaymentGatewayInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayStarPaymentGateway implements PaymentGatewayInterface
{
    use InitiateTransactionTrait, RedirectToGatewayTrait, ConfirmTransactionTrait;

    protected $gatewayName = 'paystar';
    protected $apiRequestService;

    public function __construct(ApiRequestService $apiRequestService)
    {
        $this->apiRequestService = $apiRequestService;
    }


}
