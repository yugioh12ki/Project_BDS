<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function login()
    {
        return view('auth.login');
    }

    public function Authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials)) {
            $user = Auth::user();

            if($user->status !== 'active') {
                Auth::logout();
                return redirect()->route('login')->withErrors(['status' => 'Tài khoản của bạn bị khóa hoặc không hoạt động.']);
            }

            switch($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'Owner':
                case 'Agent':
                    return redirect()->route('agent.dashboard');
                case 'user':
                    return redirect()->route('trangchu.index');
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors(['role' => 'Vai trò không hợp lệ.']);
            }
        }

        return back()->withErrors([
            'login' => 'Email hoặc mật khẩu không đúng.',
        ]);

    }
}
