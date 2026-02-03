<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Payments\PaymentGatewayInterface;
use App\Payments\PaymentGatewayResponse;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function process(Order $order, array $payload): PaymentGatewayResponse
    {
        $status = $payload['simulate_status'] ?? 'successful';

        return new PaymentGatewayResponse(
            $status,
            'cc_' . uniqid(),
            [
                'gateway' => 'credit_card',
                'api_key' => $this->config['api_key'] ?? null,
            ]
        );
    }
}
