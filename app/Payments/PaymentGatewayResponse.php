<?php

namespace App\Payments;

class PaymentGatewayResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $reference = null,
        public readonly array $metadata = []
    ) {
    }
}
