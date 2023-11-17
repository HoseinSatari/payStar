<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait CallbackTrait
{
    public function callback(Request $request)
    {
        if (!$request->gateway) {
            return abort(404);
        }
        $allGateway = array_keys($this->paymentGatewayManager->getAllGateways());
        $isGatewayExists = in_array($request->gateway, $allGateway);

        if (!$isGatewayExists) {
            Log::error('Gateway does not exist - gateway in request : ' . $request->gateway . 'ip : ' . $request->ip());
            return abort(404);
        }

        $gateway = $this->paymentGatewayManager->getGateway($request->gateway);
        $result = $gateway->confirmTransaction($request);

        return redirect()->route($result['route'], $result['params'])->with($result['type'], $result['message']);
    }
}
