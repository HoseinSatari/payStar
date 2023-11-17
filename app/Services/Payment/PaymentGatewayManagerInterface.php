<?php

namespace App\Services\Payment;

interface PaymentGatewayManagerInterface
{
    public function getAllGateways(): array;

    public function getGateway(string $gatewayName): PaymentGatewayInterface;
}
