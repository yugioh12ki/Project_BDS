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
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        // Lấy thông tin đăng nhập
        $credentials = $request->only('email', 'password');

        // Tìm người dùng theo email
        $user = User::where('Email', $credentials['email'])->first();

        // Kiểm tra nếu người dùng tồn tại và mật khẩu khớp với hash MD5
        if ($user && $user->PasswordHash === md5($credentials['password'])) {
            // Kiểm tra trạng thái người dùng
            if (!$user->isActive()) { // Sử dụng phương thức isActive() từ model User
                return redirect()->back()->with('error', 'Tài khoản của bạn đã bị khóa hoặc chưa được kích hoạt');
            }

            // Đăng nhập người dùng
            Auth::login($user);

            session(['name' => $user->Name]);

            // Điều hướng theo vai trò
            return match ($user->Role) {
                'Admin' => redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!'),
                'Owner' => redirect()->route('owner.dashboard')->with('success', 'Đăng nhập thành công!'),
                'Agent' => redirect()->route('agent.dashboard')->with('success', 'Đăng nhập thành công!'),
                'Customer' => redirect()->route('home')->with('success', 'Đăng nhập thành công!'),
                default => redirect()->route('login')->withErrors(['role' => 'Vai trò không hợp lệ.']),
            };
        }

        // Nếu thông tin đăng nhập không chính xác
        return back()->with('error', 'Email hoặc mật khẩu không chính xác. Vui lòng thử lại!');
    }

}
