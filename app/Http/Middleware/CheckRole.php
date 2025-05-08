<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            Log::info('User is logged in', ['user' => $user]);
        } else {
            Log::info('User is not logged in');
        }


        if(Auth::check() && Auth::user()->Role === $role)
        {
            return $next($request);
        }

        abort(403,'Bạn không có quyền truy cập vào trang');
        //dd(Auth::user());
    }
}
