<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class UserController extends Controller
{
    public function getUser() {
        if (!Auth::check()) return;
        $user = auth()->user();
        return response()->json([
            'id'       => $user->id,
            'username' => $user->username,
            'color'    => $user->color,
            'role'     => $user->role,
        ]);
    }

    public function loginSubmit(Request $request) {
        $success = Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ]);
        return $success ? redirect(route('index')) : redirect()->back();
    }

    public function loginPage(Request $request) {
        return view('login');
    }
}