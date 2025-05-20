<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Property;

class SharePendingPropertyCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Đếm số lượng BĐS đang chờ duyệt
        $pendingPropertyCount = Property::where('Status', 'pending')->count();

        // Chia sẻ biến với tất cả các view
        View::share('pendingPropertyCount', $pendingPropertyCount);

        return $next($request);
    }
}
