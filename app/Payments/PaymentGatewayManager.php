<?php

namespace App\Payments;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function resolve(string $method): PaymentGatewayInterface
    {
        $gatewayConfig = config("payment.gateways.{$method}");
        if (!$gatewayConfig || !Arr::get($gatewayConfig, 'class')) {
            throw new InvalidArgumentException("Unsupported payment method: {$method}");
        }

        $class = $gatewayConfig['class'];
        $config = $gatewayConfig['config'] ?? [];

        return new $class($config);
    }
}
