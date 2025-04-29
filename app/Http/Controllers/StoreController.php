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
            return [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address,
                'logo' => $store->getFirstMediaUrl('logos') ?: null,
            ];
        });
    
        return response()->json($stores);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $store = Store::create($validated);

        if ($request->hasFile('logo')) {
            $store->addMediaFromRequest('logo')->toMediaCollection('logos');
        }

        return response()->json($store->load('media'), 201);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $store->update($validated);

        if ($request->hasFile('logo')) {
            $store->clearMediaCollection('logos');
            $store->addMediaFromRequest('logo')->toMediaCollection('logos');
        }

        return response()->json($store->load('media'));
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
