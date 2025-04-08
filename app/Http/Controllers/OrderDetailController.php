<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;

class OrderDetailController extends Controller{

    public function updateDispatched(Request $request, $id)
    {
        $detail = OrderDetail::findOrFail($id);

        $validated = $request->validate([
            'dispatched_quantity' => 'required|integer|min:0',
        ]);

        $detail->dispatched_quantity = $validated['dispatched_quantity'];
        $detail->save();

        return response()->json($detail);
    }
    
}