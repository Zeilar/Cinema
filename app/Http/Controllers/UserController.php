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
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        
        $success = Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ]);
        
        if ($success) return redirect(route('index'));

        if (count(User::where('username', $request->username)->get())) {
            $errors = ['password' => 'Incorrect password'];
        } else {
            $errors = ['username' => 'That user does not exist'];
        }

        return redirect()->back()->withInput()->withErrors($errors);
    }

    public function loginPage(Request $request) {
        return view('login');
    }

    public function logout() {
        Auth::logout();
        return redirect(route('index'));
    }
}