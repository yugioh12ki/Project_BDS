<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\TransactionController;
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
        // Route::get('/property/create', [OwnerController::class, 'createPropertyForm'])->name('property.create');
        // Route::post('/property', [OwnerController::class, 'createProperty'])->name('property.store');
        // Route::get('/property/get-for-listing', [OwnerController::class, 'getPropertiesForListing'])->name('property.get-for-listing');
        Route::post('/property/listings', [OwnerController::class, 'storePropertyListing'])->name('property.listings.store');
        Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
        Route::get('/appointments/filter/{status}', [OwnerController::class, 'getAppointmentsByStatus'])->name('appointments.filter');
        Route::post('/appointments/{id}/confirm', [OwnerController::class, 'confirmAppointment'])->name('appointments.confirm');
        Route::post('/appointments/{id}/cancel', [OwnerController::class, 'cancelAppointment'])->name('appointments.cancel');
        Route::post('/appointments/{id}/finish', [OwnerController::class, 'finishAppointment'])->name('appointments.finish');
        Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions.index');
        
        // Notifications route
        Route::post('/appointments/update-status/{id}', [OwnerController::class, 'updateAppointmentStatus'])->name('appointment.updateStatus');
        Route::get('/notifications-agent', [OwnerController::class, 'getNotifications'])->name('notifications-agent');
        Route::get('/notifications-owner', [OwnerController::class, 'getNotifications'])->name('notifications-owner');

        // Profile and Password routes
        Route::get('/profile', [OwnerController::class, 'showProfile'])->name('profile');
        Route::post('/profile', [OwnerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [OwnerController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [OwnerController::class, 'changePassword'])->name('change-password.update');
    });

    // Agent routes
    Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/brokers', [AgentController::class, 'brokers'])->name('brokers');
        Route::get('/property/{id}', [AgentController::class, 'propertyDetails'])->name('property.details');
        Route::get('/api/property/{id}', [AgentController::class, 'getPropertyDetails'])->name('api.property.details');
        Route::get('/appointments', [AgentController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/filter/{status}', [AgentController::class, 'getAppointmentsByStatus'])->name('appointments.filter.agent');
        Route::get('/profile', [AgentController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [AgentController::class, 'updateProfile'])->name('profile.update');
        Route::post('/appointments/create', [AgentController::class, 'createAppointment'])->name('appointments.create');
        Route::put('/appointments/{id}/status', [AgentController::class, 'updateAppointmentStatus'])->name('appointments.update-status');
        Route::get('/transactions', [AgentController::class, 'index'])->name('transactions');
        Route::post('/transactions', [AgentController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/search', [AgentController::class, 'search'])->name('transactions.search');
        Route::get('/transactions/export', [AgentController::class, 'export'])->name('transactions.export');
        Route::get('/transactions/{id}', [AgentController::class, 'show'])->name('transactions.show');
        Route::get('/properties/{id}/customers', [AgentController::class, 'getRelatedCustomers'])->name('properties.customers');
        Route::get('/search-owners', [AgentController::class, 'searchOwners'])->name('search.owners');
        Route::get('/search-customers', [AgentController::class, 'searchCustomers'])->name('search.customers');
        Route::get('/search-properties', [AgentController::class, 'searchProperties'])->name('search.properties');
        
        Route::get('/notifications-agent', [AgentController::class, 'getNotifications'])->name('notifications.agent');

        
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
    
    // Route dự phòng để đảm bảo không bị 404 khi truy cập /property/{id}
    // Kiểm tra quyền của người dùng và chuyển hướng phù hợp
    Route::get('/property/{id}', function($id) {
        if (Auth::check() && Auth::user()->Role === 'Agent') {
            return redirect()->route('agent.property.details', ['id' => $id]);
        } elseif (Auth::check() && Auth::user()->Role === 'Owner') {
            // return redirect()->route('owner.property.view', ['id' => $id]);
            return redirect('/'); // Tạm thời chuyển về trang chủ nếu chưa có route cho owner
        } else {
            // Chuyển về trang chủ hoặc thông báo lỗi cho người dùng không xác thực
            return redirect('/');
        }
    });
});

// Debug route for properties
Route::get('/debug/properties', function() {
    $agent = \App\Models\User::where('UserID', 'UID00003')->first();
    if (!$agent) {
        return response()->json(['error' => 'Agent not found']);
    }
    
    $properties = \App\Models\Property::with(['danhMuc', 'owner', 'chiTiet', 'images'])
        ->where('AgentID', $agent->UserID)
        ->get();
    
    return response()->json([
        'agent' => $agent->toArray(),
        'total_properties' => $properties->count(),
        'active_properties' => $properties->where('Status', 'active')->count(),
        'properties' => $properties->map(function($p) {
            return [
                'id' => $p->PropertyID,
                'title' => $p->Title,
                'status' => $p->Status,
                'agent_id' => $p->AgentID,
                'has_detail' => !!$p->chiTiet,
                'images_count' => $p->images->count()
            ];
        })
    ]);
});

// Debug route to test property detail API response
Route::get('/debug/property/{id}', function($id) {
    $property = \App\Models\Property::with(['danhMuc', 'owner', 'chiTiet', 'images'])
        ->where('PropertyID', $id)
        ->first();
    
    if (!$property) {
        return response()->json(['error' => 'Property not found'], 404);
    }
    
    // Format response similar to AgentController::getProperty
    $response = [
        'success' => true,
        'property' => [
            'PropertyID' => $property->PropertyID,
            'Title' => $property->Title,
            'Description' => $property->Description,
            'Address' => $property->Address,
            'Price' => $property->Price,
            'TypePro' => $property->TypePro,
            'Status' => $property->Status,
            'Latitude' => $property->Latitude,
            'Longitude' => $property->Longitude,
            'images' => $property->images,
            'danhMuc' => $property->danhMuc,
            'owner' => $property->owner,
            'chiTiet' => $property->chiTiet
        ]
    ];
    
    return response()->json($response);
});

// Test API endpoint for modal (matches agent API format)
Route::get('/test/api/property/{id}', function($id) {
    // Mock property data that matches the AgentController::getProperty response format
    $mockProperties = [
        'PR00001' => [
            'success' => true,
            'property' => [
                'PropertyID' => 'PR00001',
                'Title' => 'Cho Thuê Chung Cư Mini Sactaim',
                'Description' => 'Chung cư sẽ có các điều hòa, wifi miễn phí, gần trường học và chợ',
                'Address' => '12 Ca Văn Thỉnh, Phường 8, Vũng Tàu',
                'Price' => 12000000,
                'TypePro' => 'Rent',
                'Status' => 'active',
                'Latitude' => 10.3414,
                'Longitude' => 107.0839,
                'images' => [
                    [
                        'ImageID' => 3,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-1.jpg',
                        'Caption' => 'Sảnh phòng trọ'
                    ],
                    [
                        'ImageID' => 4,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-2.jpg',
                        'Caption' => 'Phòng của nhà thuê'
                    ],
                    [
                        'ImageID' => 5,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-3.jpg',
                        'Caption' => 'Phòng bếp hiện đại'
                    ],
                    [
                        'ImageID' => 6,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-4.jpg',
                        'Caption' => 'Ban công view đẹp'
                    ]
                ],
                'danhMuc' => [
                    'Protype_ID' => 3,
                    'ten_pro' => 'Chung cư mini',
                    'Type' => 'Chung cư'
                ],
                'owner' => [
                    'UserID' => 'UID00007',
                    'Name' => 'Nguyễn Hoàng Phát',
                    'Email' => 'nguyenphat241203@gmail.com',
                    'Phone' => '0855542696',
                    'Address' => '65/20 Nguyen Do Cung, Phường Tây Thạnh, Quận Tân Phú, TP.HCM'
                ],
                'chiTiet' => [
                    'IdDetail' => 1,
                    'PropertyID' => 'PR00001',
                    'Floor' => 2,
                    'HouseLength' => 12,
                    'HouseWidth' => 15,
                    'Bedroom' => 4,
                    'Balcony' => 1,
                    'Bath_WC' => 3,
                    'legal' => 'Sổ Đỏ/ Sổ Hồng',
                    'view' => 'Tây Bắc',
                    'near' => 'Gần Chợ Bách Hóa Xanh, GS25',
                    'Interior' => 'Cơ Bản',
                    'WaterPrice' => 'Thỏa thuận',
                    'PowerPrice' => 'Thỏa thuận',
                    'Utilities' => 'Thỏa thuận'
                ]
            ]
        ],
        'PS00002' => [
            'success' => true,
            'property' => [
                'PropertyID' => 'PS00002',
                'Title' => 'Cho Bán Phòng 2 phòng của Sactaim',
                'Description' => 'Phòng cho thuê giá rẻ, vị trí đẹp, gần trung tâm',
                'Address' => '123 Nguyễn Văn Linh, Quận 7, TP.HCM',
                'Price' => 25000000,
                'TypePro' => 'Sale',
                'Status' => 'active',
                'Latitude' => 10.7308,
                'Longitude' => 106.7185,
                'images' => [],
                'danhMuc' => [
                    'Protype_ID' => 2,
                    'ten_pro' => 'Nhà phố',
                    'Type' => 'Nhà phố'
                ],
                'owner' => [
                    'UserID' => 'UID00007',
                    'Name' => 'Nguyễn Hoàng Phát',
                    'Email' => 'nguyenphat241203@gmail.com',
                    'Phone' => '0855542696',
                    'Address' => '65/20 Nguyen Do Cung, Phường Tây Thạnh, Quận Tân Phú, TP.HCM'
                ],
                'chiTiet' => [
                    'IdDetail' => 2,
                    'PropertyID' => 'PS00002',
                    'Floor' => 3,
                    'HouseLength' => 8,
                    'HouseWidth' => 20,
                    'Bedroom' => 3,
                    'Balcony' => 2,
                    'Bath_WC' => 2,
                    'legal' => 'Sổ Hồng',
                    'view' => 'Đông Nam',
                    'near' => 'Gần siêu thị, trường học',
                    'Interior' => 'Đầy đủ',
                    'WaterPrice' => '50.000 VNĐ/tháng',
                    'PowerPrice' => '3.500 VNĐ/kwh',
                    'Utilities' => 'Điện, nước, internet'
                ]
            ]
        ]
    ];
    
    if (!isset($mockProperties[$id])) {
        return response()->json(['success' => false, 'message' => 'Property not found'], 404);
    }
    
    return response()->json($mockProperties[$id]);
});

// Test route for modal without authentication
Route::get('/test/modal', function() {
    // Create fake properties data for testing  
    $properties = collect([
        (object)[
            'PropertyID' => 'PR00001',
            'Title' => 'Cho Thuê Chung Cư Mini Sactaim',
            'Description' => 'Chung cư sẽ có các điều hòa',
            'Address' => '12 Ca Văn Thỉnh',
            'Price' => 12000000,
            'TypePro' => 'Rent',
            'Status' => 'active',
            'images' => collect([
                (object)['ImagePath' => 'public\\storage\\properties\\images\\anh-1.jpg', 'Caption' => 'Sảnh phòng trọ']
            ]),
            'danhMuc' => (object)['ten_pro' => 'Chung cư mini'],
            'owner' => (object)[
                'Name' => 'Nguyễn Hoàng Phát',
                'Phone' => '0855542696',
                'Email' => 'nguyenphat241203@gmail.com'
            ]
        ],
        (object)[
            'PropertyID' => 'PS00002',
            'Title' => 'Cho Bán Phòng 2 phòng của Sactaim',
            'Description' => 'Phòng cho thuê giá rẻ',
            'Address' => '123 Nguyễn Văn Linh',
            'Price' => 25000000,
            'TypePro' => 'Sale',
            'Status' => 'active',
            'images' => collect([]),
            'danhMuc' => (object)['ten_pro' => 'Nhà phố'],
            'owner' => (object)[
                'Name' => 'Nguyễn Hoàng Phát',
                'Phone' => '0855542696',
                'Email' => 'nguyenphat241203@gmail.com'
            ]
        ]
    ]);

    $categories = collect([]);
    $owners = collect([]);

    return view('agents.brokers', compact('properties', 'categories', 'owners'));
});
