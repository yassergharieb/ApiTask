<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Payments\PaymentGatewayInterface;
use App\Payments\PaymentGatewayResponse;

class PaypalGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function process(Order $order, array $payload): PaymentGatewayResponse
    {
        $status = $payload['simulate_status'] ?? 'successful';

        return new PaymentGatewayResponse(
            $status,
            'pp_' . uniqid(),
            [
                'gateway' => 'paypal',
                'client_id' => $this->config['client_id'] ?? null,
            ]
        );
    }
}
