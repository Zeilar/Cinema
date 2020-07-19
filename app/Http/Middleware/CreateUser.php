<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Auth;

class CreateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) Auth::login(factory(User::class)->create());
        return $next($request);
    }
}