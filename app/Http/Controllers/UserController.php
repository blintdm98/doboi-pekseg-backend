<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function login()
    {
        return view('pages.users.login');
    }

    public function destroy(): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        session()->flush();
        auth()->logout();

        return redirect('login')->with('success', __('user.goodbye'));
    }

    public function index()
    {
        return response()->json(User::all());
    }
}
