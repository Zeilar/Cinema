<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\User;
use Auth;

class UserController extends Controller
{
    public function pushStatus(Request $request) {
        if (Auth::check()) {
            $user = auth()->user();
            Cache::add('user-online-' . $user->id, $user, 5);
        }
    }
}