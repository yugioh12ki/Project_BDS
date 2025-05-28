<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //
    public function register()
    {
        return view('auth.register');
    }

    public function formRegister(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user,Email', // Sửa table name cho đúng
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = \App\Models\User::create([
            'Name' => $request->name,
            'Email' => $request->email,
            'PasswordHash' => $request->password, // MD5 sẽ được áp dụng tự động qua setPasswordAttribute
            'Role' => 'Customer', // Default role
            'StatusUser' => 'active', // Default status
        ]);

        // Redirect or return a response
        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }
}
