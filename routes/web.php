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
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/property', [OwnerController::class, 'listProperty'])->name('property.index');
        Route::get('/property/create', [OwnerController::class, 'createPropertyForm'])->name('property.create');
        Route::post('/property', [OwnerController::class, 'createProperty'])->name('property.store');
        Route::get('/property/get-for-listing', [OwnerController::class, 'getPropertiesForListing'])->name('owner.property.get-for-listing');
        Route::post('/property/listings', [OwnerController::class, 'storePropertyListing'])->name('property.listings.store');
        Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
        Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions.index');

        // Profile and Password routes
        Route::get('/profile', [OwnerController::class, 'showProfile'])->name('profile');
        Route::post('/profile', [OwnerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [OwnerController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [OwnerController::class, 'changePassword'])->name('change-password.update');
    });

    // Agent routes
    Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/brokers', [AgentController::class, 'brokers'])->name('brokers');  // Route cho phân công môi giới
        Route::get('/appointments', [AgentController::class, 'appointments'])->name('appointments'); // Route cho lịch hẹn
        Route::get('/transactions', [AgentController::class, 'transactions'])->name('transactions'); // Route cho giao dịch
        Route::get('/profile', [AgentController::class, 'profile'])->name('profile');
        Route::post('/profile', [AgentController::class, 'updateProfile'])->name('profile.update');
    });

    // Route đăng xuất (áp dụng chung cho tất cả quyền)
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    // Transaction Route trong OwnerController
    Route::get('/transaction/{id}', [OwnerController::class, 'showTransaction'])->name('transaction.view');
    Route::get('/transaction/{id}/print', [OwnerController::class, 'printInvoice'])->name('transaction.print');
    Route::get('/export/transactions', [OwnerController::class, 'exportTransactions'])->name('export.transactions');

    // Test route để kiểm tra API
    Route::get('/test-properties-api', function() {
        try {
            $ownerId = Auth::user()->UserID;
            echo "<h2>Test API Get Properties for Listing</h2>";
            echo "<p><strong>Current User ID:</strong> " . $ownerId . "</p>";
            echo "<p><strong>Current User Role:</strong> " . Auth::user()->Role . "</p>";

            // Test query trực tiếp
            $ownerProperties = \App\Models\Property::with(['danhMuc', 'chiTiet', 'images'])
                ->where('OwnerID', $ownerId)
                ->get();

            echo "<p><strong>Properties found:</strong> " . $ownerProperties->count() . "</p>";

            if ($ownerProperties->count() > 0) {
                echo "<h3>Properties Details:</h3>";
                foreach ($ownerProperties as $property) {
                    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
                    echo "<p><strong>ID:</strong> " . $property->PropertyID . "</p>";
                    echo "<p><strong>Title:</strong> " . $property->Title . "</p>";
                    echo "<p><strong>Owner ID:</strong> " . $property->OwnerID . "</p>";
                    echo "<p><strong>Type:</strong> " . $property->TypePro . "</p>";
                    echo "<p><strong>Price:</strong> " . number_format($property->Price) . "</p>";
                    echo "<p><strong>Address:</strong> " . $property->Address . "</p>";
                    echo "<p><strong>Images count:</strong> " . $property->images->count() . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p style='color: red;'>Không tìm thấy bất động sản nào cho Owner ID: " . $ownerId . "</p>";

                // Kiểm tra tất cả properties
                $allProperties = \App\Models\Property::all();
                echo "<p><strong>Total properties in system:</strong> " . $allProperties->count() . "</p>";

                if ($allProperties->count() > 0) {
                    echo "<h3>All Properties Owner IDs:</h3>";
                    foreach ($allProperties as $prop) {
                        echo "<p>Property " . $prop->PropertyID . " - Owner: " . $prop->OwnerID . "</p>";
                    }
                }
            }

            echo "<hr>";
            echo "<h3>Test API Endpoint:</h3>";
            echo "<button onclick='testAPI()'>Test Get Properties API</button>";
            echo "<div id='api-result'></div>";

            echo "<script>
                async function testAPI() {
                    try {
                        const response = await fetch('/owner/property/get-for-listing', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        document.getElementById('api-result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    } catch (error) {
                        document.getElementById('api-result').innerHTML = '<p style=\"color: red;\">Error: ' + error.message + '</p>';
                    }
                }
            </script>";

        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    })->name('test.properties.api');
});
