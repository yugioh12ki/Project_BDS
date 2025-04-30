<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class SystemController extends Controller
{
    //
    public function admin()
    {
        return view( '_system.index');
    }

    public function getUser()
    {
        $users = User::all(); // Sửa $user thành $users
        return view('_system.users', compact('users')); // Đảm bảo biến truyền vào view là $users
    }
}
