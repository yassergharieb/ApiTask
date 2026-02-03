<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(User $user): array
    {
        $token = app(JwtService::class)->createToken($user);

        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_user_can_create_order(): void
    {
        $user = User::factory()->create();

        $payload = [
            'customer_name' => 'Acme Corp',
            'customer_email' => 'acme@example.com',
            'items' => [
                ['product_name' => 'Widget', 'quantity' => 2, 'price' => 10.5],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload, $this->authHeader($user));

        $response->assertCreated()
            ->assertJsonPath('data.total', 21.0)
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_payment_requires_confirmed_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
        ], $this->authHeader($user));

        $response->assertStatus(422);
    }

    public function test_user_can_process_payment_for_confirmed_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'simulate_status' => 'successful',
        ], $this->authHeader($user));

        $response->assertCreated()
            ->assertJsonPath('data.payment_status', 'successful');
    }
}
