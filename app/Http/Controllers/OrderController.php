<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->with('items')
            ->orderByDesc('id')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn ($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'total' => $total,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);
            }

            return $order->load('items');
        });

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        return new OrderResource($order->load('items'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $this->authorizeOrder($order);

        $data = $request->validated();

        $order = DB::transaction(function () use ($order, $data) {
            if (array_key_exists('items', $data)) {
                $order->items()->delete();
                $total = collect($data['items'])->sum(fn ($item) => $item['price'] * $item['quantity']);

                foreach ($data['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'line_total' => $item['price'] * $item['quantity'],
                    ]);
                }

                $order->total = $total;
            }

            $order->fill(collect($data)->except('items')->toArray());
            $order->save();

            return $order->load('items');
        });

        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->payments()->exists()) {
            return response()->json(
                ['message' => 'Order cannot be deleted while it has payments.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted.'], Response::HTTP_OK);
    }

    private function authorizeOrder(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have access to this order.');
        }
    }
}
