<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::query()
            ->whereHas('order', fn ($query) => $query->where('user_id', auth()->id()))
            ->with('order')
            ->orderByDesc('id')
            ->paginate(10);

        return PaymentResource::collection($payments);
    }

    public function byOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have access to this order.');
        }

        $payments = $order->payments()->orderByDesc('id')->paginate(10);

        return PaymentResource::collection($payments);
    }

    public function store(ProcessPaymentRequest $request, PaymentService $paymentService)
    {
        $data = $request->validated();
        $order = Order::where('user_id', auth()->id())->findOrFail($data['order_id']);

        try {
            $payment = $paymentService->process($order, $data['payment_method'], $data);
        } catch (InvalidArgumentException $exception) {
            return response()->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return (new PaymentResource($payment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
