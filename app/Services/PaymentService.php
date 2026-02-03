<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\PaymentGatewayManager;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly PaymentGatewayManager $gatewayManager)
    {
    }

    public function process(Order $order, string $method, array $payload = []): Payment
    {
        if ($order->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'order_id' => 'Payments can only be processed for confirmed orders.',
            ]);
        }

        $gateway = $this->gatewayManager->resolve($method);
        $response = $gateway->process($order, $payload);

        return Payment::create([
            'order_id' => $order->id,
            'payment_status' => $response->status,
            'payment_method' => $method,
            'payment_reference' => $response->reference,
            'metadata' => $response->metadata,
        ]);
    }
}
