<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemController extends Controller
{
    //
    public function admin()
    {
        return view( '_system.index');
    }
}
