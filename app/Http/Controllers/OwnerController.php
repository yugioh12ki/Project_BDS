<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function owner()
    {
        return view('owner.dashboard');
    }
}
