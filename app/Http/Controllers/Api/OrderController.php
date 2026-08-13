<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Models\Order;


class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());

        return (new OrderResource($order->load('ticket.event')))
            ->response()
            ->setStatusCode(201);
    }

    //where('user_id', auth()->id()) filters at the query level,
    //so no order belonging to someone else is ever returned.
public function index()
{
    $orders = Order::where('user_id', auth()->id())
        ->with('ticket.event')
        ->latest()
        ->get();

    return OrderResource::collection($orders);
}
}