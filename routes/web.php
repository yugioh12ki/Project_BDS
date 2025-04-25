<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use PHPUnit\Event\Telemetry\System;
use App\Http\Controllers\SystemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[HomeController::class,"index"])->name('home');
Route::get('/login',[LoginController::class,"login"])->name('login');
Route::get('/register',[RegisterController::class,"register"])->name('register');

Route::prefix('admin')->group(
    function () {
        Route::get('/dashboard',[SystemController::class,"admin"])->name('dashboard');
    }
);
