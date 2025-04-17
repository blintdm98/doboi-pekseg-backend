<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all()->map(function ($store) {
            $store->logo = $store->logo
                ? asset('storage/' . $store->logo)
                : null;
            return $store;
        });
    
        return response()->json($stores);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|url',
        ]);
    
        $store = Store::create($validated);
        return response()->json($store, 201);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|url',
        ]);
    
        $store->update($validated);
        return response()->json($store);
    }

    public function destroy($id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json(['message' => 'Üzlet nem található'], 404);
        }

        $store->delete();

        return response()->json(['message' => 'Üzlet sikeresen törölve']);
    }
}
