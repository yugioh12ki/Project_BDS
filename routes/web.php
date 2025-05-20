<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\RegisterController;
use PHPUnit\Event\Telemetry\System;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;

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

//Trang chủ
Route::get('/',[HomeController::class,"index"])->name('home');

//Đăng Nhập
Route::get('/login',[LoginController::class,"login"])->name('login');
Route::post('/login', [LoginController::class, "authenticate"])->name('login.authenticate');

//Đăng Ký
Route::get('/register',[RegisterController::class,"register"])->name('register');

// Demo route - không cần auth
Route::get('/property-cards-demo', function () {
    // Tạo dữ liệu mẫu cho card BĐS
    $properties = collect([
        (object)[
            'PropertyID' => 'P001',
            'Title' => 'Căn hộ cao cấp, Trung tâm',
            'Address' => '123 Nguyễn Huệ',
            'Ward' => 'Quận 1',
            'District' => 'Quận 1',
            'Province' => 'TP.HCM',
            'Price' => 5200000000,
            'TypePro' => 'Sale',
            'images' => collect([
                (object)['ImageURL' => 'https://via.placeholder.com/600x400', 'IsThumbnail' => 1]
            ]),
            'danhMuc' => (object)['ten_pro' => 'Căn hộ'],
            'chiTiet' => (object)['Area' => 85, 'Bedroom' => 2, 'Bath_WC' => 2]
        ],
        (object)[
            'PropertyID' => 'P002',
            'Title' => 'Nhà phố liền kề',
            'Address' => '456 Lê Văn Lương',
            'Ward' => 'Quận 7',
            'District' => 'Quận 7',
            'Province' => 'TP.HCM',
            'Price' => 35000000,
            'TypePro' => 'Rent',
            'images' => collect([
                (object)['ImageURL' => 'https://via.placeholder.com/600x400', 'IsThumbnail' => 1]
            ]),
            'danhMuc' => (object)['ten_pro' => 'Nhà phố'],
            'chiTiet' => (object)['Area' => 120, 'Bedroom' => 3, 'Bath_WC' => 3]
        ],
        (object)[
            'PropertyID' => 'P003',
            'Title' => 'Biệt thự view sông',
            'Address' => '789 Nguyễn Văn Linh',
            'Ward' => 'Quận 7',
            'District' => 'Quận 7',
            'Province' => 'TP.HCM',
            'Price' => 25000000000,
            'TypePro' => 'Sale',
            'images' => collect([
                (object)['ImageURL' => 'https://via.placeholder.com/600x400', 'IsThumbnail' => 1]
            ]),
            'danhMuc' => (object)['ten_pro' => 'Biệt thự'],
            'chiTiet' => (object)['Area' => 350, 'Bedroom' => 5, 'Bath_WC' => 6]
        ]
    ]);

    $categories = collect([
        (object)['Protype_ID' => 1, 'ten_pro' => 'Căn hộ'],
        (object)['Protype_ID' => 2, 'ten_pro' => 'Nhà phố'],
        (object)['Protype_ID' => 3, 'ten_pro' => 'Biệt thự']
    ]);

    $owners = collect([]);

    return view('owners.property.index', compact('properties', 'categories', 'owners'));
});

Route::middleware(['auth'])->group(function () {

    // Admin routes
    Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function() {
        Route::get('/', [SystemController::class, 'admin'])->name('dashboard');
        Route::get('/property', [SystemController::class, "getProperty"])->name('property');
        Route::get('/user', [SystemController::class, "getUser"])->name('users');
        Route::get('/user/create', [SystemController::class, "createUserForm"])->name('users.create');
        Route::post('/user/create', [SystemController::class, 'createUser'])->name('users.store');
        Route::get('/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('users.byRole');
        Route::get('/appointment', [SystemController::class, "getAppointment"])->name('appointment');
        Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');
        Route::get('/feedback', [SystemController::class, "getFeedback"])->name('feedback');
        Route::get('/commission', [SystemController::class, "getCommission"])->name('commission');
    });

    // Owner routes
    Route::middleware(['checkRole:Owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/property', [OwnerController::class, 'listProperty'])->name('property.index');
        Route::get('/property/create', [OwnerController::class, 'createProperty'])->name('property.create');
        Route::post('/property', [OwnerController::class, 'storeProperty'])->name('property.store');
        Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
        Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions.index');
    });

    // Giả sử thêm 2 quyền nữa (ví dụ: Agent và Customer)
    Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
        // các route khác của agent...
    });

    // Route::middleware(['checkrole:customer'])->prefix('customer')->name('customer.')->group(function () {
    //     Route::get('/', [CustomerController::class, 'dashboard'])->name('dashboard');
    //     // các route khác của customer...
    // });

    // Route đăng xuất (áp dụng chung cho tất cả quyền)
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});
