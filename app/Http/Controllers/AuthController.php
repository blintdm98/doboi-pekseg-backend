<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_name' => 'required|string',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Hibás bejelentkezési adatok'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('mobile-token')->plainTextToken;

        if ($user->role === 'admin') {
            $stores = \App\Models\Store::select('id', 'name', 'address', 'phone', 'contact_person')->get();
        } else {
            $stores = $user->stores()->select('stores.id', 'stores.name', 'stores.address', 'stores.phone', 'stores.contact_person')->get();
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'can_add_store' => $user->can_add_store,
            ],
            'stores' => $stores,
            'token' => $token,
        ]);
    }
}
