<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        return view("trangchu.index");
    }

    public function getUser($id)
    {
        return response()->json(User::find($id));
    }
}
