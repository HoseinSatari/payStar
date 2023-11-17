<?php

namespace App\Services\Payment;

use App\Services\ApiRequest\ApiRequestService;
use App\Services\Payment\Paystar\PayStarPaymentGateway;
use Illuminate\Support\Facades\Log;

class PaymentGatewayManager implements PaymentGatewayManagerInterface
{
    protected $gateways = [];

    public function __construct(array $paymentGateway = [])
    {
        $this->gateways = $paymentGateway == [] ? ['paystar' => new PayStarPaymentGateway(new ApiRequestService())] : $paymentGateway;
    }

    public function getAllGateways(): array
    {
        return $this->gateways;
    }

    public function getGateway(string $gatewayName): PaymentGatewayInterface
    {
        if (isset($this->gateways[$gatewayName])) {
            return $this->gateways[$gatewayName];
        } else {
            $errorMessage = "Gateway '$gatewayName' not found.";
            Log::error($errorMessage);

            throw new \InvalidArgumentException($errorMessage);
        }
    }
}
