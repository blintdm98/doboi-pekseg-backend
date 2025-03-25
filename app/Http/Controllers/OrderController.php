<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with('orderDetails')->get());
    }

    public function store(Request $request)
    {
        $order = Order::create([
            'store_id' => $request->store_id,
            'user_id' => $request->user_id,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::with('orderDetails.product')->findOrFail($id);
        return response()->json($order);
    }
}
