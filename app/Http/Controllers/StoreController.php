<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            $stores = Store::all();
        } else {
            $stores = $user->stores;
        }
        
        $stores = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address,
                'phone' => $store->phone,
                'contact_person' => $store->contact_person,
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

        if ($store->orders()->exists()) {
            return response()->json(['message' => 'Nem törölhető: aktív rendelései vannak.'], 403);
        }

        $store->delete();

        return response()->json(['message' => 'Üzlet sikeresen törölve']);
    }
}
