<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Enums\OrderStatuses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->query('store_id');

        $orders = Order::with(['orderDetails.product', 'user'])
            ->where('store_id', $storeId)
            ->when($request->has('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->query('start_date'));
            }, function ($query) {
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subMonth());
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_name' => $order->user->name ?? 'N/A',
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'comment' => $order->comment,
                    'total' => $order->orderDetails->sum(function ($detail) {
                        $price = $detail->price ?? $detail->product->price ?? 0;
                        return $detail->quantity * $price;
                    }),
                    'details' => $order->orderDetails->map(function ($detail) {
                        $price = $detail->price ?? $detail->product->price ?? 0;
                        return [
                            'product_name' => $detail->product->name ?? 'N/A',
                            'quantity' => $detail->quantity,
                            'dispatched_quantity' => $detail->dispatched_quantity,
                            'subtotal' => $detail->quantity * $price,
                        ];
                    }),
                ];
            });
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        // Ellenőrizzük, hogy van-e termék a rendelésben
        if (empty($request->items) || !is_array($request->items)) {
            return response()->json([
                'error' => 'A rendelésnek tartalmaznia kell legalább egy terméket'
            ], 400);
        }

        // Ha van is_returned paraméter, akkor visszaküldött rendelés
        $status = $request->has('is_returned') && $request->is_returned
            ? OrderStatuses::RETURNED->value 
            : OrderStatuses::PENDING->value;
        
        $order = Order::create([
            'store_id' => $request->store_id,
            'user_id' => $request->user_id,
            'status' => $status,
            'comment' => $request->comment,
        ]);
        
        logger($request->items);
        foreach ($request->items as $item) {
            // Ellenőrizzük, hogy a termék létezik és a mennyiség pozitív
            if (!isset($item['product_id']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                return response()->json([
                    'error' => 'Érvénytelen termék adatok'
                ], 400);
            }
            
            $product = Product::find($item['product_id']);
            if (!$product) {
                return response()->json([
                    'error' => 'Termék nem található'
                ], 404);
            }
            
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'dispatched_quantity' => 0,
                'price' => $product->price,
                'tva' => $product->tva ?? 11,
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
