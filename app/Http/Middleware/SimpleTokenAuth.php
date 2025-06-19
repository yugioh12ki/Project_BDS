<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SimpleTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Lấy token từ header Authorization hoặc query parameter
        $token = $request->header('Authorization');

        if (!$token && $request->has('token')) {
            $token = $request->input('token');
        }

        if (!$token) {
            return response()->json([
                'error' => 'Token required',
                'message' => 'Authorization token is required for this endpoint'
            ], 401);
        }
          // Loại bỏ 'Bearer ' prefix nếu có
        $token = str_replace('Bearer ', '', $token);

        // Validate token format: UserID|timestamp|random
        try {
            // Decode token (base64)
            $decodedToken = base64_decode($token);

            if (!$decodedToken || !str_contains($decodedToken, '|')) {
                return response()->json([
                    'error' => 'Invalid token format',
                    'message' => 'Token format is invalid'
                ], 401);
            }

            // Parse token: UserID|timestamp|random
            $tokenParts = explode('|', $decodedToken);

            if (count($tokenParts) !== 3) {
                return response()->json([
                    'error' => 'Invalid token structure',
                    'message' => 'Token structure is invalid'
                ], 401);
            }

            list($userId, $timestamp, $random) = $tokenParts;

            // Tìm user bằng UserID
            $user = User::where('UserID', $userId)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'User not found',
                    'message' => 'Invalid token - user not found'
                ], 401);
            }

            // Kiểm tra token expiration (optional - 30 days)
            $tokenAge = time() - (int)$timestamp;
            $maxAge = 30 * 24 * 60 * 60; // 30 days in seconds

            if ($tokenAge > $maxAge) {
                return response()->json([
                    'error' => 'Token expired',
                    'message' => 'Token has expired, please login again'
                ], 401);
            }

            // Gắn user vào request để sử dụng trong controller
            $request->merge(['authenticated_user' => $user]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token validation failed',
                'message' => 'Could not validate token: ' . $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
