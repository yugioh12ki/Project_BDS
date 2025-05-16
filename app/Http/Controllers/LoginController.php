<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

//Hàm sử dụng model

use App\Models\User;

class LoginController extends Controller
{
    //
    public function login()
    {
        return view('auth.login');
    }

    // public function Authenticate(Request $request) // Hàm xử lý đăng nhập
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:4',
    //     ]);

    //     $credentials = $request->only('email', 'password');
    //     if(Auth::attempt($credentials)) {
    //         $user = Auth::user();

    //         if($user->status !== 'active') {
    //             Auth::logout();
    //             return redirect()->route('login')->withErrors(['status' => 'Tài khoản của bạn bị khóa hoặc không hoạt động.']);
    //         }

    //         switch($user->role) {
    //             case 'admin':
    //                 return redirect()->route('admin.dashboard');
    //             case 'Owner':
    //             case 'Agent':
    //                 return redirect()->route('agent.dashboard');
    //             case 'user':
    //                 return redirect()->route('trangchu.index');
    //             default:
    //                 Auth::logout();
    //                 return redirect()->route('login')->withErrors(['role' => 'Vai trò không hợp lệ.']);
    //         }
    //     }

    //     return back()->withErrors([
    //         'login' => 'Email hoặc mật khẩu không đúng.',
    //     ]);

    // }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Lấy thông tin đăng nhập
        $credentials = $request->only('email', 'password');

        // Tìm người dùng theo email
        $user = User::where('Email', $credentials['email'])->first();

        if ($user && $user->PasswordHash == $credentials['password']) { // So sánh trực tiếp mật khẩu


            // Kiểm tra trạng thái người dùng
            if (!$user->isActive()) { // Sử dụng phương thức isActive() từ model User
                Auth::logout();
                return redirect()->back()->withErrors(['status' => 'Tài khoản của bạn bị khóa']);
            }

            // Đăng nhập người dùng
            Auth::login($user);
            //dd(Auth::user());

            session(['name' => $user->Name]);

            // Điều hướng theo vai trò
            return match ($user->Role) {
                'Admin' => redirect()->route('admin.dashboard'),
                // 'Owner', 'Agent' => redirect()->route('agent.dashboard'),
                'Customer' => redirect()->route('customer.home'),
                default => redirect()->route('login')->withErrors(['role' => 'Vai trò không hợp lệ.']),
            };



            // switch ($user->Role) {
            //     case 'Admin':
            //         return redirect()->route('admin.users');
            //     case 'Owner':
            //     case 'Agent':
            //         return redirect()->route('agent.dashboard');
            //     case 'User':
            //         return redirect()->route('trangchu.index');
            //     default:
            //         return redirect()->route('login')->withErrors(['role' => 'Vai trò không hợp lệ.']);
            // }
        }

        // Nếu thông tin đăng nhập không chính xác
        return back()->withErrors(['login' => 'Email hoặc mật khẩu không đúng.']);
    }

}
