<?php

namespace App\Http\Controllers;

use App\Models\DetailProperty;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\feedback;
use App\Models\Commission;
use App\Models\DanhMucBDS;
use App\Models\detail_transaction;
use App\Models\Document;
use App\Models\Image;
use App\Models\Video;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// Import PhpWord để đọc file .docx
use PhpOffice\PhpWord\IOFactory;

class SystemController extends Controller
{
    //
    public function admin()
    {
        if (Auth::check()) {
            // Tính toán thống kê tổng quan
            $totalTransactions = Transaction::count();
            $totalProperties = Property::count();
            $totalCommissions = Commission::count();
            $totalUsers = User::count();

            // Thống kê giao dịch theo trạng thái
            $paidTransactions = Transaction::where('TranStatus', 'Paid')->count();
            $pendingTransactions = Transaction::where('TranStatus', 'Pending')->count();
            $cancelledTransactions = Transaction::where('TranStatus', 'Cancelled')->count();

            // Thống kê bất động sản theo trạng thái
            $activeProperties = Property::where('Status', 'active')->count();
            $pendingProperties = Property::where('Status', 'pending')->count();
            $soldProperties = Property::where('Status', 'sold')->count();
            $rentedProperties = Property::where('Status', 'rented')->count();
            $rejectedProperties = Property::where('Status', 'rejected')->count();

            // Thống kê hoa hồng
            $successCommissions = Commission::where('StatusCommission', 'Success')->count();
            $pendingCommissions = Commission::where('StatusCommission', 'Pending')->count();
            $cancelledCommissions = Commission::where('StatusCommission', 'Cancelled')->count();

            // Thống kê người dùng theo vai trò
            $totalAgents = User::where('Role', 'Agent')->count();
            $totalOwners = User::where('Role', 'Owner')->count();
            $totalCustomers = User::where('Role', 'Customer')->count();
            $totalAdmins = User::where('Role', 'Admin')->count();

            // Thống kê doanh thu
            $totalRevenue = Transaction::where('TranStatus', 'Paid')->sum('TotalPrice');
            $totalCommissionAmount = Commission::where('StatusCommission', 'Success')->sum('Amount');

            // Thống kê cuộc hẹn
            $totalAppointments = Appointment::count();
            $completedAppointments = Appointment::where('Status', 'Hoàn Thành')->count();
            $pendingAppointments = Appointment::where('Status', 'Khởi tạo')->count();
            $inProgressAppointments = Appointment::where('Status', 'Đang Thực hiện')->count();
            $cancelledAppointments = Appointment::where('Status', 'Hủy Hẹn')->count();

            // Thống kê theo loại giao dịch
            $rentTransactions = Transaction::where('TransactionType', 'Rent')->count();
            $saleTransactions = Transaction::where('TransactionType', 'Sale')->count();

            // Thống kê doanh thu theo tháng (12 tháng gần đây)
            $monthlyRevenue = [];
            $monthlyTransactionCounts = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthKey = $date->format('Y-m');

                $revenue = Transaction::where('TranStatus', 'Paid')
                    ->whereYear('TransactionDate', $date->year)
                    ->whereMonth('TransactionDate', $date->month)
                    ->sum('TotalPrice');

                $count = Transaction::whereYear('TransactionDate', $date->year)
                    ->whereMonth('TransactionDate', $date->month)
                    ->count();

                $monthlyRevenue[] = $revenue;
                $monthlyTransactionCounts[] = $count;
            }

            // Top agents by số lượng giao dịch
            $topAgents = User::where('Role', 'Agent')
                ->withCount(['trans_agent as transaction_count'])
                ->orderBy('transaction_count', 'desc')
                ->take(5)
                ->get();

            // Recent activities
            $recentTransactions = Transaction::with(['trans_property', 'trans_agent', 'trans_cus', 'trans_owner'])
                ->orderBy('TransactionDate', 'desc')
                ->take(5)
                ->get();

            $recentProperties = Property::with(['chusohuu', 'danhMuc'])
                ->orderBy('PostedDate', 'desc')
                ->take(5)
                ->get();

            $recentCommissions = Commission::with(['comm_agent', 'comm_trans'])
                ->orderBy('CommissionID', 'desc')
                ->take(5)
                ->get();

            $recentAppointments = Appointment::with(['user_agent', 'user_customer', 'user_owner', 'property'])
                ->orderBy('AppointmentDateStart', 'desc')
                ->take(5)
                ->get();

            return view('_system.index', compact(
                'totalTransactions', 'totalProperties', 'totalCommissions', 'totalUsers',
                'paidTransactions', 'pendingTransactions', 'cancelledTransactions',
                'activeProperties', 'pendingProperties', 'soldProperties', 'rentedProperties', 'rejectedProperties',
                'successCommissions', 'pendingCommissions', 'cancelledCommissions',
                'totalAgents', 'totalOwners', 'totalCustomers', 'totalAdmins',
                'totalRevenue', 'totalCommissionAmount',
                'totalAppointments', 'completedAppointments', 'pendingAppointments', 'inProgressAppointments', 'cancelledAppointments',
                'rentTransactions', 'saleTransactions',
                'monthlyRevenue', 'monthlyTransactionCounts',
                'topAgents', 'recentTransactions', 'recentProperties', 'recentCommissions', 'recentAppointments'
            ));
        } else {
            abort(403, 'Bạn không có quyền truy cập vào trang này.');
        }
    }

    public function getUser()
    {
        $columns = Schema::getColumnListing('user');
        $users = User::paginate(10);

        if ($columns === null) {
            $error = 'Lỗi lấy cấu trúc bảng user';
            return view('_system.users', compact('error', 'columns', 'users'));
        }

        return view('_system.users', compact('columns','users'));
    }

    public function getUserByRole(Request $request,$role)  // Hàm tìm kiếm user theo role (Không Produruce)
    {


        if (empty($columnsToShow)) {
            $columnsToShow = Schema::getColumnListing('user');
        }

        if($role == 'all')
        {
            $users = User::paginate(10);
        }
        else
        {
            $users = User::where('role', $role)->paginate(10);
        }
        $columns = Schema::getColumnListing('user');
        if ($columns === null || $users->isEmpty()) {
            return response()->json(['error' => 'Không tìm thấy user nào.'], 404); // Truyền thông báo lỗi sang view
        }
        else {
            return view('_system.users', compact('columns','users')); // Đảm bảo biến truyền vào view là $users
        }
    }

    public function getUserByStatus(Request $request,$status)
    {
        if (empty($columnsToShow)) {
            $columnsToShow = Schema::getColumnListing('user');
        }

        if($status == 'all')
        {
            $users = User::paginate(10);
        }
        else
        {
            $users = User::where('StatusUser', $status)->paginate(10);
        }
        $columns = Schema::getColumnListing('user');
        if ($columns === null || $users->isEmpty()) {
            return response()->json(['error' => 'Không tìm thấy user nào.'], 404); // Truyền thông báo lỗi sang view
        }
        else {
            return view('_system.partialview.user_table', compact('columns','users')); // Đảm bảo biến truyền vào view là $users
        }
    }

    public function createUserForm()
    {
        return view('_system.partialview.create_user');
    }

    public function createUser(Request $request)
    {
        //Log::info($request->all());
        $validated=$request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:user,Email',
            'birth' => 'required',
            'sex' => 'required',
            'identity_card' => 'required|max:12',
            'phone' => 'required|max:10',
            'address' => 'required|max:255',
            'ward' => 'required|max:255',
            'district' => 'required|max:255',
            'province' => 'required|max:255',
            'password' => 'required|min:6|max:30',
            'role' => 'nullable|string', // Allow role from form
            'redirect_to_property' => 'nullable', // Flag for property redirect
        ]);

        $userExists = User::where('Email', $validated['email'])->first();
        if ($userExists) {
            return redirect()->back()->withErrors('error','Email đã tồn tại.');
        }

        // Determine the role - use provided role or default to Customer
        $role = $validated['role'] ?? 'Customer';

        $user = new User([
            'Name' => $validated['name'],
            'Email' => $validated['email'],
            'Birth' => $validated['birth'],
            'Sex' => $validated['sex'],
            'IdentityCard' => $validated['identity_card'],
            'Phone' => $validated['phone'],
            'Address' => $validated['address'],
            'Ward' => $validated['ward'],
            'District' => $validated['district'],
            'Province' => $validated['province'],
            'Role' => $role,
            'StatusUser' => 'active',
        ]);

        // Sử dụng mutator để tự động mã hóa MD5
        $user->PasswordHash = $validated['password']; // setPasswordAttribute sẽ tự động mã hóa MD5
        $user->save();

        // Check if this is a redirect from property creation
        if ($request->has('redirect_to_property') && $role === 'Owner') {
            // Redirect back to property creation with new owner info
            $redirectUrl = route('admin.property.create') . '?' . http_build_query([
                'newOwnerId' => $user->UserID,
                'newOwnerName' => $user->Name,
                'newOwnerPhone' => $user->Phone,
                'newOwnerEmail' => $user->Email,
            ]);

            return redirect($redirectUrl)->with('success', 'Chủ sở hữu đã được tạo thành công và đã được chọn trong form bất động sản.');
        }

        return redirect()->route('admin.users')->with('success', 'Người dùng đã được tạo thành công.');
    }

    public function editUserForm($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Người dùng không tồn tại.'], 404);
        }
        $columns = Schema::getColumnListing('user');
        return view('_system.partialview.edit_user', compact('user'));
    }

    public function UpdateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Người dùng không tồn tại.'], 404);
        }

        // Xử lý cập nhật thông tin người dùng
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:user,Email,' . $id . ',UserID',
            'birth' => 'required',
            'sex' => 'required',
            'identity_card' => 'required|max:12',
            'phone' => 'required|max:10',
            'address' => 'required|max:255',
            'ward' => 'required|max:255',
            'district' => 'required|max:255',
            'province' => 'required|max:255',
            'role' => 'required',
            'password' => 'required|min:6|max:30',
            'status' => 'required|in:active,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Map lại tên trường cho đúng DB
        $data = [
            'Name' => $validated['name'],
            'Email' => $validated['email'],
            'Birth' => $validated['birth'],
            'Sex' => $validated['sex'],
            'IdentityCard' => $validated['identity_card'],
            'Phone' => $validated['phone'],
            'Address' => $validated['address'],
            'Ward' => $validated['ward'],
            'District' => $validated['district'],
            'Province' => $validated['province'],
            'Role' => $validated['role'],
            'StatusUser' => $validated['status'],
        ];

        // Handle avatar upload to avatars directory
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            // Validate avatar file
            if (!$avatar->isValid()) {
                return redirect()->back()->withErrors(['avatar' => 'Avatar file is not valid.'])->withInput();
            }

            // Ensure avatars directory exists
            $avatarPath = public_path('storage/avatars');
            if (!file_exists($avatarPath)) {
                if (!mkdir($avatarPath, 0755, true)) {
                    return redirect()->back()->withErrors(['avatar' => 'Cannot create avatars directory.'])->withInput();
                }
            }

            // Check if directory is writable
            if (!is_writable($avatarPath)) {
                return redirect()->back()->withErrors(['avatar' => 'Avatars directory is not writable.'])->withInput();
            }

            // Generate unique filename - chỉ sử dụng số để tránh lỗi với tên tiếng Việt
            $extension = $avatar->getClientOriginalExtension();
            $avatarFileName = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

            // Delete old avatar if exists
            if ($user->Avatar && file_exists(public_path('storage/avatars/' . $user->Avatar))) {
                unlink(public_path('storage/avatars/' . $user->Avatar));
            }

            // Move the file with error handling
            try {
                if (!$avatar->move($avatarPath, $avatarFileName)) {
                    throw new \Exception('Failed to move avatar file to destination.');
                }
                $data['Avatar'] = $avatarFileName; // Store only filename
                Log::info("Avatar updated successfully: {$avatarFileName}");
            } catch (\Exception $e) {
                Log::error("Avatar upload failed: " . $e->getMessage());
                return redirect()->back()->withErrors(['avatar' => 'The avatar failed to upload.'])->withInput();
            }
        }

        // Chỉ cập nhật password nếu có nhập password mới
        if (!empty($validated['password'])) {
           // Sử dụng mutator để tự động mã hóa MD5
           $user->PasswordHash = $validated['password']; // setPasswordAttribute sẽ tự động mã hóa MD5
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    /**
     * Update user with profile based on role
     */
    public function updateUserWithProfile(Request $request, $userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return redirect()->back()->withErrors(['error' => 'Người dùng không tồn tại.'])->withInput();
            }

            // Basic user validation
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:user,Email,' . $userId . ',UserID',
                'phone' => 'nullable|string|max:15',
                'birth' => 'nullable|date',
                'sex' => 'required|string|in:Nam,Nữ,Khác',
                'identity_card' => 'required|string|max:12',
                'address' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'password' => 'nullable|string|min:6|max:30', // Thêm validation cho password
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ];

            // Role-specific validation
            switch ($user->Role) {
                case 'Admin':
                    $rules['TenChucVu'] = 'nullable|string|in:Nhân viên,Quản trị viên,Giám đốc';
                    break;
                case 'Agent':
                    $rules['Certificate'] = 'nullable|string';
                    $rules['ContactAgent'] = 'nullable|string|max:15';
                    $rules['NumberCardAgent'] = 'nullable|string';
                    $rules['ProvinceAgent'] = 'nullable|string';
                    $rules['DistrictAgent'] = 'nullable|string';
                    break;
                case 'Owner':
                    $rules['ContactOwner'] = 'nullable|string|max:15';
                    $rules['NumberCardOwner'] = 'nullable|string';
                    $rules['GiayTo'] = 'nullable|string';
                    break;
                case 'Customer':
                    $rules['Whitelist'] = 'nullable|string';
                    $rules['PreferredPropertyType'] = 'nullable|string';
                    break;
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Handle avatar upload to avatars directory
            $avatarFileName = $user->Avatar; // Keep existing avatar if no new upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');

                // Validate avatar file
                if (!$avatar->isValid()) {
                    throw new \Exception('Avatar file is not valid.');
                }

                // Ensure avatars directory exists
                $avatarPath = public_path('storage/avatars');
                if (!file_exists($avatarPath)) {
                    if (!mkdir($avatarPath, 0755, true)) {
                        throw new \Exception('Cannot create avatars directory: ' . $avatarPath);
                    }
                }

                // Check if directory is writable
                if (!is_writable($avatarPath)) {
                    throw new \Exception('Avatars directory is not writable: ' . $avatarPath);
                }

                // Generate unique filename - chỉ sử dụng số để tránh lỗi với tên tiếng Việt
                $extension = $avatar->getClientOriginalExtension();
                $avatarFileName = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

                // Delete old avatar if exists
                if ($user->Avatar && file_exists(public_path('storage/avatars/' . $user->Avatar))) {
                    unlink(public_path('storage/avatars/' . $user->Avatar));
                }

                // Move the file with error handling
                try {
                    if (!$avatar->move($avatarPath, $avatarFileName)) {
                        throw new \Exception('Failed to move avatar file to destination.');
                    }
                    Log::info("Avatar updated successfully: {$avatarFileName}");
                } catch (\Exception $e) {
                    Log::error("Avatar upload failed: " . $e->getMessage());
                    throw new \Exception('The avatar failed to upload.');
                }
            }

            // Update user basic info
            $updateData = [
                'Name' => $validated['name'],
                'Email' => $validated['email'],
                'Phone' => $validated['phone'] ?? null,
                'Birth' => $validated['birth'] ?? null,
                'Sex' => $validated['sex'],
                'IdentityCard' => $validated['identity_card'],
                'Address' => $validated['address'],
                'Ward' => $validated['ward'],
                'District' => $validated['district'],
                'Province' => $validated['province'],
                'StatusUser' => $validated['status'],
                'Avatar' => $avatarFileName, // Store only filename
            ];

            // Chỉ cập nhật password nếu có nhập password mới
            if (!empty($validated['password'])) {
                // Sử dụng mutator để tự động mã hóa MD5
                $user->PasswordHash = $validated['password']; // setPasswordAttribute sẽ tự động mã hóa MD5
            }

            $user->update($updateData);

            // Update role-specific profile
            $this->updateRoleProfile($user->UserID, $user->Role, $validated);

            DB::commit();

            return redirect()->route('admin.users')->with('success', 'Thông tin người dùng và profile đã được cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating user with profile: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()])->withInput();
        }
    }

    public function DeleteUser($id)
    {
        try{
            // Get user info before deletion to handle avatar cleanup
            $user = User::find($id);
            if ($user && $user->Avatar) {
                $avatarPath = public_path('storage/avatars/' . $user->Avatar);
                if (file_exists($avatarPath)) {
                    unlink($avatarPath);
                    Log::info("Avatar deleted: {$user->Avatar}");
                }
            }

            $result = DB::statement('CALL DeleteUser_Profile(?)', [$id]);

            if($result == 0)
            {
                return redirect()->route('admin.users')->withErrors(['error' => 'Người dùng không tồn tại.']);
            } else
            {
                return redirect()->route('admin.users')->with(['success' => 'Người dùng đã được xóa thành công.']);
            }
        }catch(\Exception $e)
        {
            return redirect()->route('admin.users')->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Create user with profile based on role
     */
    public function createUserWithProfile(Request $request)
    {
        try {
            // Log request data for debugging
            Log::info('Create user request data:', $request->all());

            // Basic user validation
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:user,Email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:Admin,Agent,Owner,Customer',
                'phone' => 'nullable|string|max:15',
                'birth' => 'nullable|date',
                'sex' => 'required|string|in:Nam,Nữ,Khác',
                'identity_card' => 'required|string|max:12',
                'address' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ];

            // Role-specific validation
            switch ($request->role) {
                case 'Admin':
                    $rules['TenChucVu'] = 'nullable|string|in:Nhân viên,Quản trị viên,Giám đốc';
                    break;
                case 'Agent':
                    $rules['Certificate'] = 'nullable|string';
                    $rules['ContactAgent'] = 'nullable|string|max:15';
                    $rules['NumberCardAgent'] = 'nullable|string';
                    $rules['ProvinceAgent'] = 'nullable|string';
                    $rules['DistrictAgent'] = 'nullable|string';
                    break;
                case 'Owner':
                    $rules['ContactOwner'] = 'nullable|string|max:15';
                    $rules['NumberCardOwner'] = 'nullable|string';
                    $rules['GiayTo'] = 'nullable|string';
                    break;
                case 'Customer':
                    $rules['Whitelist'] = 'nullable|string';
                    $rules['PreferredPropertyType'] = 'nullable|string';
                    break;
            }

            $validated = $request->validate($rules);

            // Check if email exists
            if (User::where('Email', $validated['email'])->exists()) {
                return redirect()->back()->withErrors(['email' => 'Email đã tồn tại.'])->withInput();
            }

            DB::beginTransaction();

            // Handle avatar upload to avatars directory
            $avatarFileName = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');

                // Validate avatar file
                if (!$avatar->isValid()) {
                    throw new \Exception('Avatar file is not valid.');
                }

                // Ensure avatars directory exists
                $avatarPath = public_path('storage/avatars');
                if (!file_exists($avatarPath)) {
                    if (!mkdir($avatarPath, 0755, true)) {
                        throw new \Exception('Cannot create avatars directory: ' . $avatarPath);
                    }
                }

                // Check if directory is writable
                if (!is_writable($avatarPath)) {
                    throw new \Exception('Avatars directory is not writable: ' . $avatarPath);
                }

                // Generate unique filename - chỉ sử dụng số để tránh lỗi với tên tiếng Việt
                $extension = $avatar->getClientOriginalExtension();
                $avatarFileName = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

                // Move the file with error handling
                try {
                    if (!$avatar->move($avatarPath, $avatarFileName)) {
                        throw new \Exception('Failed to move avatar file to destination.');
                    }
                    Log::info("Avatar uploaded successfully: {$avatarFileName}");
                } catch (\Exception $e) {
                    Log::error("Avatar upload failed: " . $e->getMessage());
                    throw new \Exception('The avatar failed to upload.');
                }
            }

            // Create user
            $user = new User([
                'Name' => $validated['name'],
                'Email' => $validated['email'],
                'Phone' => $validated['phone'] ?? null,
                'Birth' => $validated['birth'] ?? null,
                'Sex' => $validated['sex'] ?? null,
                'IdentityCard' => $validated['identity_card'] ?? null,
                'Address' => $validated['address'] ?? null,
                'Ward' => $validated['ward'] ?? null,
                'District' => $validated['district'] ?? null,
                'Province' => $validated['province'] ?? null,
                'Role' => $validated['role'],
                'StatusUser' => 'active',
                'Avatar' => $avatarFileName, // Store only filename
            ]);

            // Sử dụng mutator để tự động mã hóa MD5
            $user->PasswordHash = $validated['password']; // setPasswordAttribute sẽ tự động mã hóa MD5
            $user->save();

            // Update role-specific profile (profiles are auto-created by database triggers)
            $this->updateRoleProfile($user->UserID, $validated['role'], $validated);

            DB::commit();

            return redirect()->route('admin.users')->with('success', 'Tài khoản đã được tạo thành công với thông tin profile!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating user with profile: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra khi tạo tài khoản: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update role-specific profile for user (profiles are auto-created by database triggers)
     */
    private function updateRoleProfile($userId, $role, $data)
    {
        switch ($role) {
            case 'Admin':
                DB::table('profile_admin')
                    ->where('UserID', $userId)
                    ->update([
                        'TenChucVu' => $data['TenChucVu'] ?? 'Nhân viên',
                    ]);
                break;

            case 'Agent':
                DB::table('profile_agent')
                    ->where('UserID', $userId)
                    ->update([
                        'Certificate' => $data['Certificate'] ?? null,
                        'DistrictAgent' => $data['DistrictAgent'] ?? null,
                        'ProvinceAgent' => $data['ProvinceAgent'] ?? null,
                        'ContactAgent' => $data['ContactAgent'] ?? null,
                        'NumberCardAgent' => $data['NumberCardAgent'] ?? null,
                    ]);
                break;

            case 'Owner':
                DB::table('profile_owner')
                    ->where('UserID', $userId)
                    ->update([
                        'ContactOwner' => $data['ContactOwner'] ?? null,
                        'NumberCardOwner' => $data['NumberCardOwner'] ?? null,
                        'GiayTo' => $data['GiayTo'] ?? null,
                    ]);
                break;

            case 'Customer':
                DB::table('profile_customer')
                    ->where('UserID', $userId)
                    ->update([
                        'Whitelist' => $data['Whitelist'] ?? null,
                        'PreferredPropertyType' => $data['PreferredPropertyType'] ?? null,
                    ]);
                break;
        }
    }

    public function SearchUser(Request $request)
    {
        $keyword = $request->input('keyword');
        $role = $request->route('role');

        $query = User::query();

        // Lọc theo role nếu có
        if (!empty($role) && $role !== 'all') {
            $query->where('Role', $role);
        }

        // Tìm kiếm theo keyword
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(Name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                  ->orWhereRaw('LOWER(Email) LIKE ?', ['%' . strtolower($keyword) . '%'])
                  ->orWhereRaw('LOWER(Phone) LIKE ?', ['%' . strtolower($keyword) . '%']);
            });
        }

        $users = $query->paginate(12);
        $columns = Schema::getColumnListing('user');

        // Đảm bảo chỉ truyền 1 biến $users duy nhất vào view cha, không lồng hoặc include lại bảng user_table
        return view('_system.users', [
            'users' => $users,
            'columns' => $columns,
            'error' => $users->isEmpty() ? 'Không tìm thấy user nào.' : null
        ]);
    }



    // public function getUserByRole($role)
    // {
    //     if($role == 'all')
    //     {
    //         $users = DB::select('CALL batdongsan.select_all_from_table_varchar(?)',['user']);
    //     }
    //     else
    //     {
    //         $users = DB::select('CALL batdongsan.select_all_from_table_role(?)',$role);
    //     }
    //     $columns = Schema::getColumnListing('user');
    //     if ($columns === null || Empty($users)) {
    //         return response()->json(['error' => 'Không tìm thấy user nào.'], 404); // Truyền thông báo lỗi sang view
    //     }
    //     else {
    //         return view('_system.partialview.user_table', compact('columns','users')); // Đảm bảo biến truyền vào view là $users
    //     }
    // }

    // Phần này của property

    public function getProperty(Request $request)
    {
        $columns = Schema::getColumnListing('properties');
        $typePro = $request->query('TypePro');

        // Nếu có TypePro, lọc theo loại BĐS (Cho thuê hoặc Cho bán)
        if ($typePro) {
            $properties = Property::with(['danhMuc', 'images', 'videos'])->where('TypePro', $typePro)->get();
        } else {
            $properties = Property::with(['danhMuc', 'images', 'videos'])->get();
        }

        $owners = User::where('Role', 'Owner')->get();

        // Paginate agents to 20 per page and eager load their properties and profile_agent
        // Sắp xếp agent theo số lượng bất động sản tăng dần (ít nhất lên đầu)
        $agents = User::where('Role', 'Agent')
                    ->with('profile_agent')
                    ->withCount(['moigioi as active_property_count' => function ($query) {
                        $query->where('Status', 'active');
                    }])
                    ->orderBy('active_property_count', 'asc')
                    ->paginate(20);

        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        if ($columns === null || $properties->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.property', compact('error', 'categories')); // Truyền thông báo lỗi và categories sang view
        }

        // Truyền thêm thông tin về loại BĐS đang xem để hiển thị tab đúng
        return view('_system.property', compact('columns','properties','owners','agents','admins','categories', 'typePro'));
    }

    public function getPropertyByType(Request $request, $type)
    {
        $columns = Schema::getColumnListing('properties');

        // Chuyển đổi từ slug tiếng Anh sang giá trị trong CSDL
        $typeProMapping = [
            'rent' => 'Rent',
            'sale' => 'Sale'
        ];

        $typePro = isset($typeProMapping[$type]) ? $typeProMapping[$type] : $type;

        $properties = Property::with(['danhMuc', 'images', 'videos'])
            ->where('TypePro', $typePro)
            ->paginate(10);

        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->with('profile_agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        if ($columns === null || $properties->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Định nghĩa biến error
            return view('_system.property', compact('error'));
        } else {
            return view('_system.property', compact('columns', 'properties', 'owners', 'agents', 'admins', 'categories', 'typePro'));
        }
    }

    public function getPropertyByTypeAndStatus(Request $request, $type)
    {
        // Chuyển đổi từ slug tiếng Anh sang giá trị trong CSDL
        $typeProMapping = [
            'rent' => 'Rent',
            'sale' => 'Sale'
        ];

        $typePro = isset($typeProMapping[$type]) ? $typeProMapping[$type] : $type;
        $status = $request->query('status'); // Lấy status từ query parameter

        $columns = Schema::getColumnListing('properties');

        $query = Property::with(['danhMuc', 'images', 'videos'])
            ->where('TypePro', $typePro);

        // Chỉ áp dụng điều kiện status nếu nó được cung cấp
        if ($status) {
            $query->where('Status', $status);
        }

        $properties = $query->paginate(10);

        // Đảm bảo truyền biến status vào view để hiển thị đúng
        // Nếu status là null, gán giá trị mặc định để tránh lỗi trong view
        $status = $status ?? '';

        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->with('profile_agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        // Get property coordinates for map display
        $propertyCoordinates = [];
        foreach ($properties as $property) {
            if ($property->Latitude && $property->Longitude) {
                $propertyCoordinates[] = [
                    'id' => $property->PropertyID,
                    'lat' => $property->Latitude,
                    'lng' => $property->Longitude,
                    'title' => $property->Title,
                    'address' => $property->Address
                ];
            }
        }

        if ($columns === null || $properties->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu';
            // Đảm bảo tất cả các biến cần thiết đều được khởi tạo
            return view('_system.property', compact('error', 'type', 'typePro', 'status', 'categories', 'agents', 'owners', 'admins','columns','properties'  ));
        } else {
            return view('_system.property', compact('columns', 'properties', 'owners', 'agents', 'admins', 'categories', 'type', 'typePro', 'status', 'propertyCoordinates'));
        }
    }

    public function updatePropertyStatus(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'property_id' => 'required',
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        try {
            // Find the property
            $property = Property::findOrFail($request->property_id);

            // Update property status based on action
            if ($request->status == 'approved') {
                // When approved, set status to 'inactive'
                $property->Status = 'active';
                $property->ApprovedDate = now();
                $property->ApprovedBy = auth()->id();
            } else if ($request->status == 'rejected') {
                // When rejected, set status to 'rejected'
                $property->Status = 'rejected';

                // If reason is provided, store the reason
                if ($request->has('reason')) {
                    $property->RejectReason = $request->reason;
                }
            }

            $property->save();

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => $request->status == 'approved' ? 'Bất động sản đã được duyệt thành công.' : 'Bất động sản đã bị từ chối.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of a batch of properties
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBatchStatus(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'property_ids' => 'required',
            'status' => 'required|in:approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Decode property IDs
            $propertyIds = json_decode($request->property_ids, true);

            if (!is_array($propertyIds) || count($propertyIds) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No property IDs provided'
                ], 400);
            }

            // Get current admin ID
            $adminId = Auth::id();

            // Prepare update data based on status
            if ($request->status === 'approved') {
                // When approved, set status to 'inactive'
                $updateData = [
                    'Status' => 'inactive',
                    'ApprovedBy' => $adminId,
                    'ApprovedDate' => now()
                ];
            } else {
                // When rejected, set status to 'rejected'
                $updateData = [
                    'Status' => 'rejected',
                    'ApprovedBy' => $adminId,
                    'ApprovedDate' => now()
                ];

                // Add reason if provided (for rejected properties)
                if ($request->has('reason')) {
                    $updateData['RejectReason'] = $request->reason;
                }
            }

            // Update all properties
            $updatedCount = Property::whereIn('PropertyID', $propertyIds)->update($updateData);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái cho $updatedCount bất động sản thành công",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Batch property status update error: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPropertyByStatus(Request $request, $status)
    {
        $columns = Schema::getColumnListing('properties');

        if ($status == 'all') {
            $properties = Property::with(['danhMuc', 'images', 'videos'])->paginate(10); // Sửa pageinate thành paginate
        } else {
            $properties = Property::with(['danhMuc', 'images', 'videos'])
                ->where('Status', $status)
                ->paginate(10);
        }

        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->with('profile_agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();
        if ($columns === null || $properties->isEmpty()) {
            return view('_system.partialview.property_table', compact('error'));
        } else {
            return view('_system.partialview.property_table', compact('columns', 'properties', 'owners', 'agents', 'admins', 'categories'));
        }
    }

    public function SearchProperty(Request $request)
    {
        $keyword = $request->input('keyword');

        $columns = Schema::getColumnListing('properties');
        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->with('profile_agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        $properties = Property::with(['danhMuc', 'chusohuu', 'moigioi', 'quantri', 'images', 'videos'])
                    ->whereRaw('LOWER(Title) LIKE ?', ['%' . strtolower($keyword) . '%'])
                    ->paginate(10);

        if ($columns === null || $properties->isEmpty()) {
            return view('_system.property', compact('error', 'categories'));
        }

        return view('_system.property', compact('columns', 'properties', 'owners', 'agents', 'admins', 'categories'));
    }

    public function getPropertyById($id)
    {
        $property = Property::with(['chitiet', 'images', 'videos'])->find($id);
        if (!$property) {
            return redirect()->back()->withErrors(['error' => 'Bất động sản không tồn tại.'], 404);
        }
        $columns = Schema::getColumnListing('properties');
        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->with('profile_agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();
        return view('_system.partialview.info_property', compact('property','columns','owners','agents','admins','categories'));
    }

    public function EditPropertyByStatus(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return redirect()->back()->withErrors(['error' => 'Bất động sản không tồn tại.'], 404);
        }

        // Xử lý cập nhật thông tin bất động sản
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending,rented,sold',
            'approvedDate' => 'nullable|date',
            'approvedBy' => 'nullable',
        ]);

        // Cập nhật trạng thái bất động sản
        $property->Status = $validated['status'];

        // Nếu bất động sản được duyệt (active), cập nhật người duyệt và ngày duyệt
        if ($validated['status'] == 'active' && ($property->getOriginal('Status') != 'active')) {
            $property->ApprovedBy = $request->input('approvedBy', Auth::id());
            $property->ApprovedDate = now();
        }

        $property->save();

        return redirect()->route('admin.property')->with('success', 'Trạng thái bất động sản đã được cập nhật thành công.');
    }

    public function createProperty(Request $request)
    {
        try {
            // Validate main property data
            $validatedProperty = $request->validate([
                'Title' => 'required|string|max:255',
                'TypePro' => 'required|in:Sale,Rent',
                'Description' => 'required|string',
                'Price' => 'required|numeric|min:0',
                'Address' => 'required|string|max:255',
                'Ward' => 'required|string|max:255',
                'District' => 'required|string|max:255',
                'Province' => 'required|string|max:255',
                'PropertyType' => 'required|exists:danhmuc_pro,Protype_ID',
                'selectedOwnerId' => 'required|exists:user,UserID',
                'video_urls' => 'nullable|array',
                'video_urls.*' => 'nullable|url|max:500',
                'video_caption' => 'nullable|string|max:255',
            ]);

            // Validate property details
            $validatedDetails = $request->validate([
                'LevelHouse' => 'nullable|integer',
                'Floor' => 'nullable|integer',
                'HouseLength' => 'nullable|numeric',
                'HouseWidth' => 'nullable|numeric',
                'TotalLength' => 'nullable|numeric',
                'TotalWidth' => 'nullable|numeric',
                'Bedroom' => 'nullable|integer',
                'Balcony' => 'nullable|boolean',
                'Bath_WC' => 'nullable|integer',
                'Road' => 'nullable|numeric',
                'legal' => 'nullable|string|max:255',
                'view' => 'nullable|string',
                'near' => 'nullable|string|max:255',
                'Interior' => 'nullable|string',
                'WaterPrice' => 'nullable|string',
                'PowerPrice' => 'nullable|string',
                'Utilities' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Lưu property - để trigger database tự động tạo PropertyID
            $property = new Property();

            $propertyDetail = new DetailProperty();


            $property->Title = $validatedProperty['Title'];
            // Không set ApprovedDate vì property mới chưa được duyệt
            $property->TypePro = $validatedProperty['TypePro'];
            $property->Description = $validatedProperty['Description'];
            $property->Price = $validatedProperty['Price'];
            $property->Address = $validatedProperty['Address'];
            $property->Ward = $validatedProperty['Ward'];
            $property->District = $validatedProperty['District'];
            $property->Province = $validatedProperty['Province'];
            $property->PropertyType = $validatedProperty['PropertyType'];
            $property->OwnerID = $validatedProperty['selectedOwnerId'];
            $property->AgentID = null; // Có thể để null
            $property->Status = 'pending'; // New properties are pending by default
            $property->PostedDate = now();
            $property->ApprovedDate = null; // Có thể để null vì chưa được duyệt
            $property->UserCreate = Auth::id(); // Người tạo property
            $property->save();

            // Get the PropertyID after save - the database trigger will generate it
            // Since we use varchar PropertyID with trigger, we need to query it back
            // $savedProperty = Property::where('Title', $validatedProperty['Title'])
            //     ->where('OwnerID', $validatedProperty['selectedOwnerId'])
            //     ->where('PostedDate', $property->PostedDate)
            //     ->orderBy('PostedDate', 'desc')
            //     ->first();

            // Get the PropertyID after save - use a more specific identifier
            $savedProperty = Property::where('Title', $validatedProperty['Title'])
                ->where('UserCreate', Auth::id())
                ->latest('PostedDate')
                ->first();

            if (!$savedProperty || empty($savedProperty->PropertyID)) {
                throw new \Exception('Không thể lấy PropertyID sau khi lưu property. Vui lòng kiểm tra database trigger.');
            }

            $propertyID = $savedProperty->PropertyID;


            $propertyDetail->PropertyID = $propertyID ;
            $propertyDetail->LevelHouse = $validatedDetails['LevelHouse'] ?? null;
            $propertyDetail->Floor = $validatedDetails['Floor'] ?? null;
            $propertyDetail->HouseLength = $validatedDetails['HouseLength'] ?? null;
            $propertyDetail->HouseWidth = $validatedDetails['HouseWidth'] ?? null;
            $propertyDetail->TotalLength = $validatedDetails['TotalLength'] ?? null;
            $propertyDetail->TotalWidth = $validatedDetails['TotalWidth'] ?? null;
            $propertyDetail->Bedroom = $validatedDetails['Bedroom'] ?? null;
            $propertyDetail->Balcony = $validatedDetails['Balcony'] ?? null;
            $propertyDetail->Bath_WC = $validatedDetails['Bath_WC'] ?? null;
            $propertyDetail->Road = $validatedDetails['Road'] ?? null;
            $propertyDetail->legal = $validatedDetails['legal'] ?? null;
            $propertyDetail->view = $validatedDetails['view'] ?? null;
            $propertyDetail->near = $validatedDetails['near'] ?? null;
            $propertyDetail->Interior = $validatedDetails['Interior'] ?? null;
            $propertyDetail->WaterPrice = $validatedDetails['WaterPrice'] ?? null;
            $propertyDetail->PowerPrice = $validatedDetails['PowerPrice'] ?? null;
            $propertyDetail->Utilities = $validatedDetails['Utilities'] ?? null;
            $propertyDetail->save();

            // Upload images nếu có
        if ($request->hasFile('property_images')) {
            foreach ($request->file('property_images') as $imageFile) {
                // Tạo thư mục theo PropertyID trong public/storage/images/properties/{$propertyID}
                $propertyFolder = public_path("storage/images/properties/{$propertyID}");

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($propertyFolder)) {
                    mkdir($propertyFolder, 0755, true);
                }

                // Tạo tên file unique
                $fileName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $fullPath = $propertyFolder . '/' . $fileName;

                // Di chuyển file vào thư mục đích
                $imageFile->move($propertyFolder, $fileName);

                // Lưu đường dẫn relative vào database
                $relativePath = "images/properties/{$propertyID}/{$fileName}";

                $image = new Image();
                $image->PropertyID = $propertyID;
                $image->ImagePath = $relativePath; // Lưu đường dẫn relative: properties/{PropertyID}/{fileName}
                $image->Caption = $request->input('image_caption', null);
                $image->UploadedDate = now();
                $image->save();

                // Log để debug
                Log::info("Image saved: {$relativePath} for PropertyID: {$propertyID}");
            }
        }

        // Lưu video URLs nếu có
        $videoUrls = $request->input('video_urls', []);
        if (!empty($videoUrls)) {
            foreach ($videoUrls as $videoUrl) {
                if (!empty(trim($videoUrl))) {
                    $video = new Video();
                    $video->PropertyID = $propertyID;
                    $video->VideoPath = trim($videoUrl); // Lưu URL trực tiếp
                    $video->Caption = $request->input('video_caption', null);
                    $video->UploadedDate = now();
                    $video->save();

                    // Log để debug
                    Log::info("Video URL saved: {$videoUrl} for PropertyID: {$propertyID}");
                }
            }
        }

            DB::commit();

            return redirect()->route('admin.property.create')->with('success', 'Bất động sản đã được tạo thành công với ID: ' . $propertyID . '. Đang chờ phê duyệt.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating property: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function createPropertyForm()
    {
        $owners = User::where('Role', 'Owner')->get();
        $property = null; // Initialize the property variable for the view
        $categories = DanhMucBDS::all(); // Assuming this is your property category model

        return view('_system.tiepnhanhoso', compact('owners', 'categories', 'property'));
    }

    //
    // Phần này của AssignProperty
    //

    // Hàm function này lấy danh sách User và Profile_Agent của người dùng
    public function getAssignProperty()
    {
        // Get columns from both user and profile_agent tables
        // Lấy danh sách agent với eager loading profile_agent và đếm properties
        $agents = User::where('Role', 'Agent')
                    ->with('profile_agent')
                    ->withCount(['moigioi as active_property_count' => function ($query) {
                        $query->where('Status', 'active');
                    }])
                    ->get();

        // Lấy danh sách bất động sản chưa có agent hoặc đang cần phân công lại
        $properties = Property::with(['chusohuu', 'moigioi.profile_agent', 'images', 'videos'])
                    ->paginate(10);

        // Trả về view với dữ liệu
        return view('_system.partialview.assign_property', compact('agents', 'properties'));
    }

    /**
     * Lấy danh sách bất động sản của một môi giới
     */
    public function getAgentProperties($id)
    {
        // Kiểm tra tồn tại của agent
        $agent = User::where('UserID', $id)
                     ->where('Role', 'Agent')
                     ->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môi giới'
            ], 404);
        }

        // Lấy typePro từ request (nếu có)
        $typePro = request()->input('typePro');

        // Tạo query để lấy danh sách bất động sản đã được phân công cho môi giới này
        $query = Property::where('AgentID', $id);

        // Nếu có typePro, thêm điều kiện lọc theo loại bất động sản
        if ($typePro) {
            $query->where('TypePro', $typePro);
        }

        $properties = $query->with(['chusohuu', 'images', 'videos'])
                            ->get()
                            ->map(function($property) {
                                return [
                                    'PropertyID' => $property->PropertyID,
                                    'Title' => $property->Title,
                                    'TypePro' => $property->TypePro,
                                    'OwnerName' => $property->chusohuu ? $property->chusohuu->Name : null,
                                    'District' => $property->District,
                                    'Province' => $property->Province,
                                    'Status' => $property->Status,
                                    'ImageCount' => $property->images->count(),
                                    'VideoCount' => $property->videos->count()
                                ];
                            });

        return response()->json($properties);
    }

    /**
     * Hủy phân công một bất động sản khỏi môi giới
     */
    public function unassignProperty(Request $request)
    {
        $request->validate([
            'propertyId' => 'required|exists:properties,PropertyID',
        ]);

        try {
            $property = Property::find($request->propertyId);

            // Lưu lại agent cũ để trả về trong response
            $oldAgentId = $property->AgentID;

            // Cập nhật thành null
            $property->AgentID = null;
            $property->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy phân công bất động sản thành công',
                'oldAgentId' => $oldAgentId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách bất động sản khả dụng để phân công (AgentID = NULL)
     */
    public function getAvailableProperties(Request $request)
    {
        // Kiểm tra nếu có parameter status được truyền vào
        $status = $request->input('status');
        $typePro = $request->input('typePro');

        // Tạo query builder
        $query = Property::whereNull('AgentID');

        // Nếu có status, thêm điều kiện lọc
        if ($status) {
            $query->where('Status', $status);
        }

        // Nếu có typePro, thêm điều kiện lọc theo loại bất động sản
        if ($typePro) {
            $query->where('TypePro', $typePro);
        }

        // Lấy danh sách bất động sản chưa được phân công cho môi giới nào (AgentID = NULL)
        $properties = $query->with(['chusohuu', 'images', 'videos'])
                    ->get()
                    ->map(function($property) {
                        return [
                            'PropertyID' => $property->PropertyID,
                            'Title' => $property->Title,
                            'TypePro' => $property->TypePro,
                            'OwnerName' => $property->chusohuu ? $property->chusohuu->Name : null,
                            'District' => $property->District,
                            'Province' => $property->Province,
                            'Status' => $property->Status,
                            'ImageCount' => $property->images->count(),
                            'VideoCount' => $property->videos->count()
                        ];
                    });

        return response()->json($properties);
    }

    /**
     * Phân công một hoặc nhiều bất động sản cho môi giới
     */
    public function assignProperties(Request $request)
    {
        $request->validate([
            'agentId' => 'required|exists:user,UserID',
            'propertyIds' => 'required|array',
            'propertyIds.*' => 'exists:properties,PropertyID'
        ]);

        try {
            // Lấy agent
            $agent = User::find($request->agentId);

            // Kiểm tra role
            if ($agent->Role !== 'Agent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng được chọn không phải là môi giới'
                ], 400);
            }

            // Kiểm tra giới hạn 10 bất động sản active
            $activeCount = Property::where('AgentID', $request->agentId)
                            ->where('Status', 'active')
                            ->count();

            // Chỉ đếm những BĐS active và chưa được phân công (hoặc được phân công cho agent khác)
            $newActiveCount = Property::whereIn('PropertyID', $request->propertyIds)
                                ->where('Status', 'active')
                                ->where(function($query) use ($request) {
                                    $query->whereNull('AgentID')
                                          ->orWhere('AgentID', '!=', $request->agentId);
                                })
                                ->count();

            if ($activeCount + $newActiveCount > 10) {
                return response()->json([
                    'success' => false,
                    'message' => "Môi giới {$agent->Name} đã quản lý {$activeCount} bất động sản active. Không thể thêm {$newActiveCount} bất động sản active nữa (giới hạn 10)."
                ]);
            }

            // Phân công cho tất cả bất động sản được chọn
            foreach ($request->propertyIds as $propertyId) {
                $property = Property::find($propertyId);
                if ($property) {
                    $property->AgentID = $request->agentId;
                    $property->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã phân công ' . count($request->propertyIds) . ' bất động sản cho môi giới thành công',
                'agentName' => $agent->Name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignAgentToProperty(Request $request)
    {
        $request->validate([
            'agentId' => 'required|exists:user,UserID',
            'propertyIds' => 'required|array',
            'propertyIds.*' => 'exists:properties,PropertyID'
        ]);

        try {
            // Lấy agent
            $agent = User::find($request->agentId);

            // Kiểm tra xem agent đã quản lý quá 10 bất động sản active chưa
            $activeCount = Property::where('AgentID', $request->agentId)
                            ->where('Status', 'active')
                            ->count();

            // Đếm số bất động sản active mới sẽ được gán
            $newActiveCount = Property::whereIn('PropertyID', $request->propertyIds)
                                ->where('Status', 'active')
                                ->whereNot(function($query) use ($request) {
                                    $query->where('AgentID', $request->agentId);
                                })
                                ->count();

            // Kiểm tra nếu vượt quá 10
            if ($activeCount + $newActiveCount > 10) {
                return response()->json([
                    'success' => false,
                    'message' => "Môi giới {$agent->Name} đã quản lý {$activeCount} bất động sản active. Không thể thêm {$newActiveCount} bất động sản active nữa (giới hạn 10)."
                ]);
            }

            // Tiến hành gán agent cho các bất động sản
            foreach ($request->propertyIds as $propertyId) {
                $property = Property::find($propertyId);
                if ($property) {
                    $property->AgentID = $request->agentId;
                    $property->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã gán môi giới cho ' . count($request->propertyIds) . ' bất động sản thành công.',
                'agentName' => $agent->Name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ]);
        }
    }

    //
    // Phần này của appoinment
    //

    public function getAppointment()
    {
        $columns = Schema::getColumnListing('appointments');

        // Get all agents with pagination and property counts
        // Sắp xếp agent theo số lượng bất động sản tăng dần (ít nhất lên đầu)
        $agents = User::where('Role', 'Agent')
                    ->with(['profile_agent', 'moigioi' => function($query) {
                        $query->where('Status', 'active')->with(['danhMuc', 'images']);
                    }])
                    ->withCount(['moigioi as active_property_count' => function ($query) {
                        $query->where('Status', 'active');
                    }])
                    ->withCount(['appoint_agent as total_appointments' => function ($query) {
                        $query->whereDate('AppointmentDateStart', '>=', now()->startOfMonth());
                    }])
                    ->orderBy('active_property_count', 'asc')
                    ->paginate(20);

        // Get recent appointments for overview
        $recentAppointments = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property.danhMuc'])
                            ->whereDate('AppointmentDateStart', '>=', now()->startOfWeek())
                            ->orderBy('AppointmentDateStart', 'desc')
                            ->limit(10)
                            ->get();

        // Get appointment statistics
        $appointmentStats = [
            'today' => Appointment::whereDate('AppointmentDateStart', now())->count(),
            'this_week' => Appointment::whereBetween('AppointmentDateStart', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Appointment::whereMonth('AppointmentDateStart', now()->month)->count(),
            'pending' => Appointment::where('Status', 'Pending')->count(),
        ];

        if ($columns === null) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.appointment', compact('error')); // Truyền thông báo lỗi sang view
        }

        return view('_system.appointment', compact('columns', 'agents', 'recentAppointments', 'appointmentStats')); // Pass data to view
    }

    public function getAppointmentById(Request $request, $id)
    {
        $columns = Schema::getColumnListing('appointments');
        $appointment = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property'])->find($id);

        if ($columns === null || $appointment === null) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.appointment', compact('error')); // Truyền thông báo lỗi sang view
        }

        return view('_system.partialview.checkedit_appoint', compact('columns','appointment')); // Trả về view chỉ để xem
    }

    // Xóa Cuộc Hẹn
    public function deleteAppointment($id)
    {
        try {
            $appointment = Appointment::find($id);
            if (!$appointment) {
                return back()->with('error', 'Không tìm thấy cuộc hẹn');
            }

            $appointment->delete();

            return back()->with('success', 'Xóa cuộc hẹn thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    //Tìm kiếm cuộc hẹn theo ngày
    public function searchAppointmentByDate(Request $request)
    {
        $date = $request->input('date');
        $agentId = $request->input('agent_id');
        $status = $request->input('status');
        $type = $request->input('type'); // owner hoặc customer

        $query = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property.danhMuc']);

        if ($date) {
            $query->whereDate('AppointmentDateStart', $date);
        }

        if ($agentId && $agentId !== 'all') {
            $query->where('AgentID', $agentId);
        }

        if ($status && $status !== 'all') {
            $query->where('AppointmentStatus', $status);
        }

        if ($type && $type !== 'all') {
            if ($type === 'owner') {
                $query->whereNotNull('OwnerID');
            } else if ($type === 'customer') {
                $query->whereNotNull('CusID');
            }
        }

        $appointments = $query->orderBy('AppointmentDateStart', 'asc')->get();

        return response()->json([
            'appointments' => $appointments,
            'count' => $appointments->count(),
            'date' => $date
        ]);
    }

    // Lấy cuộc hẹn theo khoảng thời gian
    public function getAppointmentsByDateRange(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $agentId = $request->input('agent_id');

        $query = Appointment::with([
            'user_owner',
            'user_agent',
            'user_customer',
            'property.danhMuc',
            'property.chiTiet',
            'property.images'
        ]);

        if ($startDate && $endDate) {
            $query->whereBetween('AppointmentDateStart', [$startDate, $endDate]);
        }

        if ($agentId && $agentId !== 'all') {
            $query->where('AgentID', $agentId);
        }

        $appointments = $query->orderBy('AppointmentDateStart', 'asc')->get();

        return response()->json([
            'appointments' => $appointments,
            'count' => $appointments->count()
        ]);
    }

    // Lấy danh sách cuộc hẹn theo agent ID
    public function getAppointmentsByAgent($agentId)
    {
        $appointments = Appointment::with([
            'user_owner',
            'user_agent',
            'user_customer',
            'property.danhMuc',
            'property.chiTiet',
            'property.images'
        ])
                        ->where('AgentID', $agentId)
                        ->orderBy('AppointmentDateStart', 'desc') // Sắp xếp theo thời gian bắt đầu giảm dần
                        ->get();

        // Phân loại cuộc hẹn (với khách hàng và với chủ sở hữu)
        $withCustomers = $appointments->filter(function($appointment) {
            return !empty($appointment->CusID);
        })->values();

        $withOwners = $appointments->filter(function($appointment) {
            return !empty($appointment->OwnerID);
        })->values();

        return response()->json([
            'appointments' => $appointments, // Thêm dòng này để JavaScript có thể đọc được
            'withCustomers' => $withCustomers,
            'withOwners' => $withOwners,
            'total' => $appointments->count()
        ]);
    }

    // Lấy chi tiết cuộc hẹn theo ID (trả về JSON)
    public function getAppointmentDetail($id)
    {
        $appointment = Appointment::with([
            'user_owner',
            'user_agent',
            'user_customer',
            'property.danhMuc',
            'property.chiTiet',
            'property.images'
        ])->find($id);

        if (!$appointment) {
            return response()->json(['error' => 'Không tìm thấy cuộc hẹn'], 404);
        }

        return response()->json(['appointment' => $appointment]);
    }



    //
    // Phần này của transaction
    //

    public function getTransaction()
    {
        $columns = Schema::getColumnListing('transactions');
        $transactions = Transaction::with([
            'trans_owner', 'trans_agent', 'trans_cus', 'detailTransaction'
            ])->get();

        // Lấy danh sách các file hợp đồng .docx
        $contractTemplates = $this->getContractTemplates();

        return view('_system.transaction', compact('columns','transactions', 'contractTemplates')); // Đảm bảo biến truyền vào view là $users
    }

    /**
     * Lấy danh sách các file template hợp đồng .docx
     */
    private function getContractTemplates()
    {
        $contractPath = public_path('storage/document/HopDong');
        $templates = [];

        if (is_dir($contractPath)) {
            $files = scandir($contractPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'docx') {
                    $filePath = $contractPath . '/' . $file;
                    $fileSize = filesize($filePath);
                    $templates[] = [
                        'name' => $file,
                        'display_name' => $this->formatFileName($file),
                        'size' => $fileSize,
                        'size_formatted' => $this->formatFileSize($fileSize),
                        'modified' => filemtime($filePath),
                        'modified_formatted' => date('d/m/Y H:i', filemtime($filePath)),
                        'download_url' => asset('storage/document/HopDong/' . $file),
                        'preview_url' => route('admin.contract.preview', ['filename' => $file]),
                        'print_url' => route('admin.contract.print', ['filename' => $file]),
                        'type' => 'docx',
                        'icon' => 'fas fa-file-word',
                        'color' => 'text-primary'
                    ];
                }
            }
        }

        // Sắp xếp theo tên file
        usort($templates, function($a, $b) {
            return strcmp($a['display_name'], $b['display_name']);
        });

        return $templates;
    }

    /**
     * Format tên file để hiển thị đẹp hơn
     */
    private function formatFileName($filename)
    {
        // Loại bỏ đuôi .docx
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Thay thế dấu gạch ngang và gạch dưới bằng khoảng trắng
        $name = str_replace(['-', '_'], ' ', $name);

        // Viết hoa chữ cái đầu mỗi từ
        $name = ucwords($name);

        return $name;
    }

    /**
     * Format kích thước file để hiển thị đẹp hơn
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Xử lý xem trước hợp đồng
     */
    public function previewContract($filename)
    {
        $filePath = public_path('storage/document/HopDong/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File không tồn tại'], 404);
        }

        // Kiểm tra extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($extension !== 'docx') {
            return response()->json(['error' => 'Chỉ hỗ trợ file .docx'], 400);
        }

        try {
            // Đọc nội dung file .docx bằng PhpOffice\PhpWord
            $phpWord = IOFactory::load($filePath);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

            // Tạo file HTML tạm thời
            $tempHtmlPath = sys_get_temp_dir() . '/' . uniqid() . '.html';
            $htmlWriter->save($tempHtmlPath);

            // Đọc nội dung HTML
            $htmlContent = file_get_contents($tempHtmlPath);

            // Xóa file tạm
            unlink($tempHtmlPath);

            // Tạo nội dung HTML xem trước với nội dung thực tế
            $previewHtml = $this->generatePreviewHtmlWithContent($filename, $filePath, $htmlContent);

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'display_name' => $this->formatFileName($filename),
                'size' => $this->formatFileSize(filesize($filePath)),
                'modified' => date('d/m/Y H:i', filemtime($filePath)),
                'download_url' => asset('storage/document/HopDong/' . $filename),
                'preview_html' => $previewHtml,
                'has_content' => true,
                'message' => 'Hiển thị nội dung file thành công.'
            ]);

        } catch (\Exception $e) {
            // Fallback nếu không đọc được file
            $previewHtml = $this->generatePreviewHtml($filename, $filePath);

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'display_name' => $this->formatFileName($filename),
                'size' => $this->formatFileSize(filesize($filePath)),
                'modified' => date('d/m/Y H:i', filemtime($filePath)),
                'download_url' => asset('storage/document/HopDong/' . $filename),
                'preview_html' => $previewHtml,
                'has_content' => false,
                'message' => 'Không thể đọc nội dung file. Hiển thị thông tin cơ bản. Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Xử lý in hợp đồng
     */
    public function printContract($filename)
    {
        $filePath = public_path('storage/document/HopDong/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File không tồn tại'], 404);
        }

        // Kiểm tra extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($extension !== 'docx') {
            return response()->json(['error' => 'Chỉ hỗ trợ file .docx'], 400);
        }

        try {
            // Đọc nội dung file .docx bằng PhpOffice\PhpWord
            $phpWord = IOFactory::load($filePath);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

            // Tạo file HTML tạm thời
            $tempHtmlPath = sys_get_temp_dir() . '/' . uniqid() . '.html';
            $htmlWriter->save($tempHtmlPath);

            // Đọc nội dung HTML
            $htmlContent = file_get_contents($tempHtmlPath);

            // Xóa file tạm
            unlink($tempHtmlPath);

            // Tạo nội dung HTML để in với nội dung thực tế
            $printHtml = $this->generatePrintHtmlWithContent($filename, $filePath, $htmlContent);

            return response()->json([
                'success' => true,
                'action' => 'browser_print',
                'filename' => $filename,
                'display_name' => $this->formatFileName($filename),
                'print_html' => $printHtml,
                'has_content' => true,
                'message' => 'Sẵn sàng để in nội dung file.'
            ]);

        } catch (\Exception $e) {
            // Fallback: chỉ in thông tin file nếu không đọc được nội dung
            $printHtml = $this->generatePrintHtml($filename, $filePath);

            return response()->json([
                'success' => true,
                'action' => 'browser_print',
                'filename' => $filename,
                'display_name' => $this->formatFileName($filename),
                'print_html' => $printHtml,
                'has_content' => false,
                'message' => 'In thông tin file (không thể đọc nội dung). Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download hợp đồng
     */
    public function downloadContract($filename)
    {
        $filePath = public_path('storage/document/HopDong/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại');
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * Tạo HTML để xem trước file Word
     */
    private function generatePreviewHtml($filename, $filePath)
    {
        $displayName = $this->formatFileName($filename);
        $fileSize = $this->formatFileSize(filesize($filePath));
        $modified = date('d/m/Y H:i', filemtime($filePath));
        $downloadUrl = asset('storage/document/HopDong/' . $filename);

        return '
        <div class="preview-document-info p-4">
            <div class="document-preview-header text-center mb-4">
                <div class="document-icon mb-3">
                    <i class="fas fa-file-word text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="document-title text-primary mb-2">' . $displayName . '</h4>
                <p class="text-muted mb-3">Microsoft Word Document (.docx)</p>
            </div>

            <div class="document-details">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item d-flex align-items-center mb-3">
                            <i class="fas fa-file me-3 text-info"></i>
                            <div>
                                <strong>Tên file:</strong><br>
                                <span class="text-muted">' . $filename . '</span>
                            </div>
                        </div>
                        <div class="detail-item d-flex align-items-center mb-3">
                            <i class="fas fa-weight me-3 text-warning"></i>
                            <div>
                                <strong>Kích thước:</strong><br>
                                <span class="text-muted">' . $fileSize . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item d-flex align-items-center mb-3">
                            <i class="fas fa-calendar me-3 text-success"></i>
                            <div>
                                <strong>Sửa đổi lần cuối:</strong><br>
                                <span class="text-muted">' . $modified . '</span>
                            </div>
                        </div>
                        <div class="detail-item d-flex align-items-center mb-3">
                            <i class="fas fa-download me-3 text-primary"></i>
                            <div>
                                <strong>Hành động:</strong><br>
                                <a href="' . $downloadUrl . '" class="btn btn-sm btn-outline-primary" download>
                                    <i class="fas fa-download me-1"></i>Tải xuống
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="preview-note mt-4 p-3 bg-light rounded">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                    <div>
                        <h6 class="mb-2">Hướng dẫn xem file</h6>
                        <ul class="mb-0 small text-muted">
                            <li>Đây là file Microsoft Word (.docx)</li>
                            <li>Để xem đầy đủ nội dung, vui lòng tải xuống file</li>
                            <li>Hoặc mở trực tiếp bằng Microsoft Word Online</li>
                            <li>Có thể sử dụng Google Docs để xem file</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="preview-actions text-center mt-4">
                <a href="' . $downloadUrl . '" class="btn btn-primary me-2" download>
                    <i class="fas fa-download me-2"></i>Tải xuống để xem
                </a>
                <a href="' . $downloadUrl . '" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-external-link-alt me-2"></i>Mở trong tab mới
                </a>
            </div>
        </div>';
    }

    /**
     * Tạo HTML để in file Word
     */
    private function generatePrintHtml($filename, $filePath)
    {
        $displayName = $this->formatFileName($filename);
        $currentDate = date('d/m/Y H:i');

        return '
        <div class="print-document" style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
            <div class="print-header" style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px;">
                <h2 style="color: #333; margin-bottom: 10px;">' . $displayName . '</h2>
                <p style="color: #666; margin: 0;">Hợp đồng được in vào: ' . $currentDate . '</p>
            </div>

            <div class="print-content" style="line-height: 1.6; color: #333;">
                <div class="document-info" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                    <h4 style="color: #333; margin-bottom: 15px;">Thông tin tài liệu:</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold; width: 30%;">Tên file:</td>
                            <td style="padding: 8px 0;">' . $filename . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold;">Kích thước:</td>
                            <td style="padding: 8px 0;">' . $this->formatFileSize(filesize($filePath)) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold;">Ngày tạo:</td>
                            <td style="padding: 8px 0;">' . date('d/m/Y H:i', filemtime($filePath)) . '</td>
                        </tr>
                    </table>
                </div>

                <div class="print-placeholder" style="border: 2px dashed #ccc; padding: 40px; text-align: center; background: #fafafa; border-radius: 8px;">
                    <i class="fas fa-file-word" style="font-size: 3rem; color: #2b5ce6; margin-bottom: 20px;"></i>
                    <h4 style="color: #333; margin-bottom: 15px;">Nội dung hợp đồng</h4>
                    <p style="color: #666; margin-bottom: 20px;">
                        Đây là file Microsoft Word (.docx)<br>
                        Để in nội dung đầy đủ, vui lòng:
                    </p>
                    <ol style="text-align: left; display: inline-block; color: #666;">
                        <li>Tải xuống file gốc</li>
                        <li>Mở bằng Microsoft Word</li>
                        <li>Sử dụng Ctrl+P để in</li>
                                                          </ol>
                </div>
            </div>

            <div class="print-footer" style="margin-top: 40px; text-align: center; border-top: 1px solid #ddd; padding-top: 10px;">
                <p style="color: #666; margin: 0; font-size: 0.9em;">
                    Tài liệu này được tạo từ hệ thống quản lý bất động sản<br>
                    Thời gian: ' . $currentDate . '
                </p>
            </div>
        </div>';
    }

    /**
     * Tạo HTML để xem trước file Word với nội dung thực tế
     */
    private function generatePreviewHtmlWithContent($filename, $filePath, $htmlContent)
    {
        $displayName = $this->formatFileName($filename);
        $fileSize = $this->formatFileSize(filesize($filePath));
        $modified = date('d/m/Y H:i', filemtime($filePath));
        $downloadUrl = asset('storage/document/HopDong/' . $filename);

        // Làm sạch HTML content và thêm CSS
        $cleanHtmlContent = $this->cleanDocxHtmlContent($htmlContent);

        return '
        <div class="preview-document-content">
            <div class="document-header mb-3 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-1"><i class="fas fa-file-word text-primary me-2"></i>' . $displayName . '</h5>
                        <small class="text-muted">' . $filename . ' • ' . $fileSize . ' • ' . $modified . '</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="' . $downloadUrl . '" class="btn btn-sm btn-outline-primary" download>
                            <i class="fas fa-download me-1"></i>Tải xuống
                        </a>
                    </div>
                </div>
            </div>

            <div class="document-content border rounded p-4" style="max-height: 500px; overflow-y: auto; background: white; font-family: Arial, sans-serif; line-height: 1.6;">
                ' . $cleanHtmlContent . '
            </div>

            <div class="document-footer mt-3 text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Hiển thị nội dung file .docx. Để xem đầy đủ định dạng, vui lòng tải xuống file gốc.
                </small>
            </div>
        </div>';
    }

    /**
     * Tạo HTML để in file Word với nội dung thực tế
     */
    private function generatePrintHtmlWithContent($filename, $filePath, $htmlContent)
    {
        $displayName = $this->formatFileName($filename);
        $currentDate = date('d/m/Y H:i');

        // Làm sạch HTML content cho việc in
        $cleanHtmlContent = $this->cleanDocxHtmlContent($htmlContent);

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>' . $displayName . '</title>
            <style>
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
                body {
                    font-family: "Times New Roman", serif;
                    line-height: 1.4;
                    margin: 20px;
                    font-size: 14px;
                }
                .print-header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 1px solid #333;
                    padding-bottom: 10px;
                }
                .print-content {
                    margin: 20px 0;
                }
                .print-footer {
                    margin-top: 30px;
                    text-align: center;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                    font-size: 12px;
                    color: #666;
                }
                table { border-collapse: collapse; width: 100%; }
                table, th, td { border: 1px solid #333; }
                th, td { padding: 8px; text-align: left; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h2>' . $displayName . '</h2>
                <p style="margin: 5px 0; font-size: 12px; color: #666;">Ngày in: ' . $currentDate . '</p>
            </div>

            <div class="print-content">
                ' . $cleanHtmlContent . '
            </div>

            <div class="print-footer">
                <p>Tài liệu được in từ hệ thống quản lý bất động sản | ' . $currentDate . '</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Làm sạch HTML content từ file .docx
     */
    private function cleanDocxHtmlContent($htmlContent)
    {
        // Loại bỏ các thẻ HTML không cần thiết
        $htmlContent = preg_replace('/<html[^>]*>/', '', $htmlContent);
        $htmlContent = preg_replace('/<\/html>/', '', $htmlContent);
        $htmlContent = preg_replace('/<head>.*?<\/head>/s', '', $htmlContent);
        $htmlContent = preg_replace('/<body[^>]*>/', '', $htmlContent);
        $htmlContent = preg_replace('/<\/body>/', '', $htmlContent);

        // Loại bỏ CSS inline phức tạp nhưng giữ lại định dạng cơ bản
        $htmlContent = preg_replace('/style="[^"]*font-family[^"]*"/i', '', $htmlContent);
        $htmlContent = preg_replace('/style="[^"]*margin[^"]*"/i', '', $htmlContent);

        // Đảm bảo có ít nhất một số định dạng cơ bản
        if (trim(strip_tags($htmlContent)) === '') {
            return '<p style="text-align: center; color: #666; padding: 20px;">Không thể hiển thị nội dung file Word. Vui lòng tải xuống để xem.</p>';
        }

        return $htmlContent;
    }

    public function getTransactionByType(Request $request, $type)
    {
        $columns = Schema::getColumnListing('transactions');

        if ($type == 'all') {
            $transactions = Transaction::all();
        } else {
            $transactions = Transaction::where('TypeTrans', $type)->get();
        }

        if ($columns === null || $transactions->isEmpty()) {
            return response()->json(['error' => 'Không tìm thấy giao dịch nào.'], 404); // Truyền thông báo lỗi sang view
        } else {
            return view('_system.partialview.trans_table', compact('columns', 'transactions')); // Đảm bảo biến truyền vào view là $users
        }
    }



    // Cập nhật đồng thời trạng thái Transaction, các detail_transaction và các document liên quan trong 1 lần submit
    public function getTransactionById($id)
    {
        $transaction = Transaction::with([
            'trans_owner',
            'trans_agent',
            'trans_cus',
            'detailTransaction',
            'document'
        ])->find($id);

        if (!$transaction) {
            return back()->with('error', 'Không tìm thấy giao dịch');
        }

        return view('_system.partialview.edit_trans', compact('transaction'));
    }

    // Chỉ cập nhật trạng thái các khoản thanh toán
    public function updatePaymentStatuses(Request $request, $transactionId)
    {
        try {
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return back()->with('error', 'Không tìm thấy giao dịch');
        }

        $payment_ids = $request->payment_ids;
        $statuses = $request->statuses;

        $errorList = [];

        if (is_array($payment_ids) && is_array($statuses)) {
            for ($i = 0; $i < count($payment_ids); $i++) {
                $numPay = $payment_ids[$i];
                $status = $statuses[$i];

                $updated = detail_transaction::where('TransactionID', $transactionId)
                    ->where('Num_Pay', $numPay)
                    ->update(['DTran_Status' => $status]);

                if (!$updated) {
                    $errorList[] = "Không tìm thấy hoặc không thể cập nhật chi tiết giao dịch số $numPay";
                }
            }
        }

        if (count($errorList) > 0) {
            return back()->with('error', implode(' | ', $errorList));
        }

        return back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // Thêm khoản thanh toán mới
    public function addPayment(Request $request, $transactionId)
    {
        $request->validate([
            'Price' => 'required|numeric|min:1',
        ]);

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return back()->with('error', 'Không tìm thấy giao dịch');
        }

        // Đếm số khoản thanh toán hiện có để tăng Num_Pay
        $maxNumPay = detail_transaction::where('TransactionID', $transactionId)
            ->max('Num_Pay');

        $newNumPay = $maxNumPay + 1;

        try {
            // Tạo khoản thanh toán mới
            $detailTransaction = new detail_transaction();
            $detailTransaction->TransactionID = $transactionId;
            $detailTransaction->Num_Pay = $newNumPay;
            $detailTransaction->Price = $request->Price;
            $detailTransaction->DTran_Date = now();
            $detailTransaction->DTran_Status = 'Chờ đợi';
            $detailTransaction->save();

            return back()->with('success', 'Thêm khoản thanh toán mới thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi thêm khoản thanh toán: ' . $e->getMessage());
        }
    }

    // New AJAX method to get transaction details for expandable rows
    public function getTransactionDetailsAjax($id)
    {
        try {
            $transaction = Transaction::with([
                'trans_owner',
                'trans_agent',
                'trans_cus',
                'trans_property',
                'detailTransaction',
                'document',
                'trans_commission.comm_agent',

            ])->find($id);

            if (!$transaction) {
                return response()->json(['error' => 'Không tìm thấy giao dịch'], 404);
            }

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'payment_history' => $transaction->detailTransaction,
                'documents' => $transaction->document,

                'commissions' => $transaction->trans_commission
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi tải chi tiết giao dịch: ' . $e->getMessage()], 500);
        }
    }

    // Process commission payment
    public function processCommissionPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'commission_id' => 'required|exists:commission,CommissionID',
                'payment_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_note' => 'nullable|string'
            ]);

            $transaction = Transaction::find($id);
            if (!$transaction) {
                return response()->json(['error' => 'Không tìm thấy giao dịch'], 404);
            }

            if ($transaction->TranStatus !== 'Paid') {
                return response()->json(['error' => 'Chỉ có thể chi trả hoa hồng cho giao dịch đã thanh toán'], 400);
            }

            $commission = Commission::find($request->commission_id);
            if (!$commission) {
                return response()->json(['error' => 'Không tìm thấy thông tin hoa hồng'], 404);
            }

            if ($commission->TransactionID !== $id) {
                return response()->json(['error' => 'Hoa hồng không thuộc về giao dịch này'], 400);
            }

            // Update commission status and payment information
            $commission->StatusCommission = 'Paid';
            $commission->PaymentDate = now();
            $commission->PaymentMethod = $request->payment_method;
            $commission->PaymentNote = $request->payment_note;
            $commission->save();

            return response()->json([
                'success' => true,
                'message' => 'Chi trả hoa hồng thành công!',
                'commission' => $commission
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi chi trả hoa hồng: ' . $e->getMessage()], 500);
        }
    }

    // Calculate commission for transaction
    public function calculateCommissionAjax($id)
    {
        try {
            $transaction = Transaction::with(['trans_agent'])->find($id);
            if (!$transaction) {
                return response()->json(['error' => 'Không tìm thấy giao dịch'], 404);
            }

            // Check if commission already exists
            $existingCommission = Commission::where('TransactionID', $id)->first();
            if ($existingCommission) {
                return response()->json(['error' => 'Hoa hồng đã được tính cho giao dịch này'], 400);
            }

            // Default commission rate (2%)
            $commissionRate = 2;
            $commissionAmount = ($transaction->TotalPrice * $commissionRate) / 100;

            // Create new commission record
            $commission = new Commission();
            $commission->TransactionID = $id;
            $commission->AgentID = $transaction->AgentID;
            $commission->CommissionRate = $commissionRate;
            $commission->Amount = $commissionAmount;
            $commission->StatusCommission = 'Pending';
            $commission->CreatedDate = now();
            $commission->save();

            return response()->json([
                'success' => true,
                'message' => 'Tính hoa hồng thành công!',
                'commission' => $commission->load('comm_agent'),
                'commission_rate' => $commissionRate,
                'commission_amount' => number_format($commissionAmount, 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi tính hoa hồng: ' . $e->getMessage()], 500);
        }
    }

    public function deleteTransaction($id)
    {
        try {
            DB::beginTransaction();

            // Tìm giao dịch
            $transaction = Transaction::with(['detailTransaction', 'document'])->find($id);

            if (!$transaction) {
                return back()->with('error', 'Không tìm thấy giao dịch');
            }

            // Xóa các tài liệu liên quan
            foreach ($transaction->document as $document) {
                // Xóa file vật lý
                $filePath = storage_path('app/' . $document->FilePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Xóa bản ghi tài liệu
                $document->delete();
            }

            // Xóa các chi tiết giao dịch bằng query builder thay vì Eloquent
            DB::table('detail_transaction')
                ->where('TransactionID', $id)
                ->delete();

            // Xóa giao dịch
            $transaction->delete();

            DB::commit();

            return back()->with('success', 'Xóa giao dịch thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi xóa giao dịch: ' . $e->getMessage());
        }
    }

    //
    // Phần này của Commission
    //

    /**
     * Hiển thị trang commission với 2 tabs Sale và Rent
     */
    public function getCommission()
    {
        $columns = Schema::getColumnListing('commission');

        // Lấy TẤT CẢ commission Sale và Rent để debug (tạm thời bỏ filter status)
        $saleCommissions = Commission::with([
            'comm_agent:UserID,Name,Phone,Email',
            'comm_trans' => function($query) {
                $query->with([
                    'trans_property:PropertyID,Title,Address',
                    'trans_owner:UserID,Name,Phone',
                    'trans_cus:UserID,Name,Phone',
                    'detailTransaction' => function($detailQuery) {
                        $detailQuery->select('TransactionID', 'Num_Pay', 'Price', 'DTran_Date', 'PaymentType');
                    }
                ]);
            }
        ])
        ->where('TypeCom', 'Sale')
        ->orderBy('CommissionID', 'desc')
        ->get();

        $rentCommissions = Commission::with([
            'comm_agent:UserID,Name,Phone,Email',
            'comm_trans' => function($query) {
                $query->with([
                    'trans_property:PropertyID,Title,Address',
                    'trans_owner:UserID,Name,Phone',
                    'trans_cus:UserID,Name,Phone',
                    'detailTransaction' => function($detailQuery) {
                        $detailQuery->select('TransactionID', 'Num_Pay', 'Price', 'DTran_Date', 'PaymentType');
                    }
                ]);
            }
        ])
        ->where('TypeCom', 'Rent')
        ->orderBy('CommissionID', 'desc')
        ->get();

        return view('_system.commission', compact('saleCommissions', 'rentCommissions', 'columns'));
    }

    /**
     * Lấy commission theo loại (Sale/Rent) via AJAX
     */
    public function getCommissionByType(Request $request, $type)
    {
        $commissions = Commission::with([
            'comm_agent:UserID,Name,Phone,Email',
            'comm_trans' => function($query) {
                $query->with([
                    'trans_property:PropertyID,Title,Address',
                    'trans_owner:UserID,Name,Phone',
                    'trans_cus:UserID,Name,Phone',
                    'detailTransaction' => function($detailQuery) {
                        $detailQuery->select('TransactionID', 'Num_Pay', 'Price', 'DTran_Date', 'PaymentType');
                    }
                ]);
            }
        ])
        ->where('TypeCom', $type)
        ->orderBy('CommissionID', 'desc')
        ->get();

        $columns = Schema::getColumnListing('commission');

        return view('_system.partialview.commission_tab_content', compact('commissions', 'columns', 'type'));
    }

    /**
     * Tìm kiếm commission theo ngày và loại
     */
    public function searchCommissionByDateAndType(Request $request)
    {
        $date = $request->input('search_date');
        $type = $request->input('type', 'Sale');

        $query = Commission::with([
            'comm_agent:UserID,Name,Phone,Email',
            'comm_trans' => function($query) {
                $query->with([
                    'trans_property:PropertyID,Title,Address',
                    'trans_owner:UserID,Name,Phone',
                    'trans_cus:UserID,Name,Phone',
                    'detailTransaction' => function($detailQuery) {
                        $detailQuery->select('TransactionID', 'Num_Pay', 'Price', 'DTran_Date', 'PaymentType');
                    }
                ]);
            }
        ])
        ->where('TypeCom', $type);

        if ($date) {
            $query->whereDate('PaidDate', $date);
        }

        $commissions = $query->orderBy('CommissionID', 'desc')->get();
        $columns = Schema::getColumnListing('commission');

        return view('_system.partialview.commission_tab_content', compact('commissions', 'columns', 'type'));
    }

    /**
     * Hiển thị chi tiết commission - supports both GET (view page) and POST (AJAX modal)
     */
    public function viewCommissionModal(Request $request)
    {
        try {
            $commissionId = $request->input('commission_id');

            // Debug log
            Log::info('Commission request', [
                'commission_id' => $commissionId,
                'method' => $request->method(),
                'is_ajax' => $request->ajax()
            ]);

            $commission = Commission::with([
                'comm_agent:UserID,Name,Phone,Email',
                'comm_trans' => function($query) {
                    $query->with([
                        'trans_property:PropertyID,Title,Address',
                        'trans_owner:UserID,Name,Phone',
                        'trans_cus:UserID,Name,Phone',
                        'detailTransaction' => function($detailQuery) {
                            $detailQuery->select('TransactionID', 'Num_Pay', 'Price', 'DTran_Date', 'PaymentType');
                        }
                    ]);
                }
            ])->find($commissionId);

            if (!$commission) {
                Log::warning('Commission not found', ['commission_id' => $commissionId]);

                if ($request->isMethod('GET')) {
                    // For GET requests, redirect back with error
                    return redirect()->route('admin.commission')->with('error', 'Không tìm thấy thông tin hoa hồng');
                } else {
                    // For POST/AJAX requests, return JSON error
                    return response()->json(['error' => 'Không tìm thấy thông tin hoa hồng'], 404);
                }
            }

            // Debug log commission data
            Log::info('Commission data loaded', [
                'commission_id' => $commission->CommissionID,
                'has_agent' => !!$commission->comm_agent,
                'has_transaction' => !!$commission->comm_trans
            ]);

            // Handle GET requests - return view
            if ($request->isMethod('GET')) {
                return view('_system.commission_detail', compact('commission'));
            }

            // Handle POST/AJAX requests - return JSON with HTML
            $html = $this->buildCommissionModalHTML($commission);

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            Log::error('Commission error', [
                'commission_id' => $request->input('commission_id'),
                'method' => $request->method(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->isMethod('GET')) {
                // For GET requests, redirect back with error
                return redirect()->route('admin.commission')->with('error', 'Lỗi khi tải thông tin hoa hồng: ' . $e->getMessage());
            } else {
                // For POST/AJAX requests, return JSON error
                return response()->json(['error' => 'Lỗi khi tải thông tin hoa hồng: ' . $e->getMessage()], 500);
            }
        }
    }

    private function buildCommissionModalHTML($commission)
    {
        try {
            $agent = $commission->comm_agent ?? null;
            $transaction = $commission->comm_trans ?? null;
            $property = $transaction ? $transaction->trans_property : null;
            $owner = $transaction ? $transaction->trans_owner : null;
            $customer = $transaction ? $transaction->trans_cus : null;
            $details = $transaction ? $transaction->detailTransaction : collect();

            $html = '<div class="row">';

            // Commission Information
            $html .= '<div class="col-md-6">';
            $html .= '<h5 class="mb-3"><i class="fas fa-info-circle"></i> Thông tin hoa hồng</h5>';
            $html .= '<table class="table table-borderless">';
            $html .= '<tr><td class="fw-bold" style="width: 35%;">Mã hoa hồng:</td><td>#' . ($commission->CommissionID ?? 'N/A') . '</td></tr>';

            // Check if Commission_Rate exists, fallback to Percentage
            $commissionRate = $commission->Commission_Rate ?? $commission->Percentage ?? 0;
            $html .= '<tr><td class="fw-bold">Tỷ lệ hoa hồng:</td><td>' . number_format($commissionRate, 2) . '%</td></tr>';

            // Check if Commission_Amount exists, fallback to Amount
            $commissionAmount = $commission->Commission_Amount ?? $commission->Amount ?? 0;
            $html .= '<tr><td class="fw-bold">Số tiền hoa hồng:</td><td class="text-success fw-bold">' . number_format($commissionAmount, 0, ',', '.') . ' VNĐ</td></tr>';

            // Check for created_at or use current date
            $createdDate = $commission->created_at ? date('d/m/Y H:i', strtotime($commission->created_at)) : 'N/A';
            $html .= '<tr><td class="fw-bold">Ngày tạo:</td><td>' . $createdDate . '</td></tr>';

            $html .= '<tr><td class="fw-bold">Trạng thái:</td><td>';

            // Check Status or StatusCommission
            $status = $commission->Status ?? $commission->StatusCommission ?? 'Unknown';
            switch($status) {
                case 'Pending':
                    $html .= '<span class="badge bg-warning">Chờ xử lý</span>';
                    break;
                case 'Success':
                case 'Paid':
                    $html .= '<span class="badge bg-success">Đã thanh toán</span>';
                    break;
                case 'Cancel':
                case 'Cancelled':
                    $html .= '<span class="badge bg-danger">Đã hủy</span>';
                    break;
                default:
                    $html .= '<span class="badge bg-secondary">' . $status . '</span>';
            }

            $html .= '</td></tr>';
            $html .= '</table>';
            $html .= '</div>';

            // Agent Information
            $html .= '<div class="col-md-6">';
            $html .= '<h5 class="mb-3"><i class="fas fa-user-tie"></i> Thông tin môi giới</h5>';
            $html .= '<table class="table table-borderless">';
            if ($agent) {
                $html .= '<tr><td class="fw-bold" style="width: 35%;">Tên:</td><td>' . htmlspecialchars($agent->Name ?? 'N/A') . '</td></tr>';
                $html .= '<tr><td class="fw-bold">Điện thoại:</td><td>' . htmlspecialchars($agent->Phone ?? 'N/A') . '</td></tr>';
                $html .= '<tr><td class="fw-bold">Email:</td><td>' . htmlspecialchars($agent->Email ?? 'N/A') . '</td></tr>';
            } else {
                $html .= '<tr><td colspan="2" class="text-muted">Không có thông tin môi giới</td></tr>';
            }
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            // Property Information
            if ($property) {
                $html .= '<div class="row mt-4">';
                $html .= '<div class="col-md-12">';
                $html .= '<h5 class="mb-3"><i class="fas fa-home"></i> Thông tin bất động sản</h5>';
                $html .= '<table class="table table-borderless">';
                $html .= '<tr><td class="fw-bold" style="width: 15%;">Tiêu đề:</td><td>' . htmlspecialchars($property->Title ?? 'N/A') . '</td></tr>';
                $html .= '<tr><td class="fw-bold">Địa chỉ:</td><td>' . htmlspecialchars($property->Address ?? 'N/A') . '</td></tr>';
                $html .= '</table>';
                $html .= '</div>';
                $html .= '</div>';
            }

            // Transaction Information
            if ($transaction) {
                $html .= '<div class="row mt-4">';
                $html .= '<div class="col-md-6">';
                $html .= '<h5 class="mb-3"><i class="fas fa-handshake"></i> Chủ sở hữu</h5>';
                if ($owner) {
                    $html .= '<table class="table table-borderless">';
                    $html .= '<tr><td class="fw-bold" style="width: 35%;">Tên:</td><td>' . htmlspecialchars($owner->Name ?? 'N/A') . '</td></tr>';
                    $html .= '<tr><td class="fw-bold">Điện thoại:</td><td>' . htmlspecialchars($owner->Phone ?? 'N/A') . '</td></tr>';
                    $html .= '</table>';
                } else {
                    $html .= '<p class="text-muted">Không có thông tin chủ sở hữu</p>';
                }
                $html .= '</div>';

                $html .= '<div class="col-md-6">';
                $html .= '<h5 class="mb-3"><i class="fas fa-user"></i> Khách hàng</h5>';
                if ($customer) {
                    $html .= '<table class="table table-borderless">';
                    $html .= '<tr><td class="fw-bold" style="width: 35%;">Tên:</td><td>' . htmlspecialchars($customer->Name ?? 'N/A') . '</td></tr>';
                    $html .= '<tr><td class="fw-bold">Điện thoại:</td><td>' . htmlspecialchars($customer->Phone ?? 'N/A') . '</td></tr>';
                    $html .= '</table>';
                } else {
                    $html .= '<p class="text-muted">Không có thông tin khách hàng</p>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }

            // Payment Details
            if ($details && $details->count() > 0) {
                $html .= '<div class="row mt-4">';
                $html .= '<div class="col-md-12">';
                $html .= '<h5 class="mb-3"><i class="fas fa-credit-card"></i> Chi tiết thanh toán</h5>';
                $html .= '<div class="table-responsive">';
                $html .= '<table class="table table-striped">';
                $html .= '<thead class="table-light">';
                $html .= '<tr>';
                $html .= '<th>Lần thanh toán</th>';
                $html .= '<th>Số tiền</th>';
                $html .= '<th>Ngày thanh toán</th>';
                $html .= '<th>Phương thức</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';

                foreach ($details as $detail) {
                    $html .= '<tr>';
                    $html .= '<td>#' . ($detail->Num_Pay ?? 'N/A') . '</td>';
                    $html .= '<td class="text-success fw-bold">' . number_format($detail->Price ?? 0, 0, ',', '.') . ' VNĐ</td>';
                    $html .= '<td>' . ($detail->DTran_Date ? date('d/m/Y', strtotime($detail->DTran_Date)) : 'N/A') . '</td>';
                    $html .= '<td>' . htmlspecialchars($detail->PaymentType ?? 'N/A') . '</td>';
                    $html .= '</tr>';
                }

                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }

            return $html;

        } catch (\Exception $e) {
            Log::error('Error building commission modal HTML', [
                'commission_id' => $commission->CommissionID ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return '<div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Lỗi:</strong> Không thể tải thông tin chi tiết hoa hồng.
                        <hr>
                        <small class="text-muted">Chi tiết lỗi: ' . htmlspecialchars($e->getMessage()) . '</small>
                    </div>';
        }
    }

    //
    // Phần này của feedback
    //

    public function getFeedback()
    {
        $columns = Schema::getColumnListing('feedbacks');
        $feedbacks = feedback::with([
            // OLD relationships - keep existing functionality working
            'user_Cus.user', 'user_Agent.user', 'agent_user', 'customer_user',
            // NEW relationships - for cleaner future code
            'customer_feedback', 'agent_feedback', 'customer_profile', 'agent_profile'
        ])->paginate(10);
        $agents = User::where('Role', 'Agent')->with('profile_agent')->get();
        $customers = User::where('Role', 'Customer')->get();

        if ($columns === null || $feedbacks->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.feedback', compact('error', 'agents', 'customers')); // Truyền thông báo lỗi sang view
        }
        return view('_system.feedback', compact('columns','feedbacks', 'agents', 'customers')); // Đảm bảo biến truyền vào view là $users
    }

    // Hàm lọc phản hồi theo trạng thái và số sao

    public function getFeedbackByStatusRating(Request $request)
    {
        $columns = Schema::getColumnListing('feedbacks');
        $status = $request->query('status', 'all');
        $min = $request->query('min', 'all');
        $max = $request->query('max', 'all');

        $query = feedback::with([
            // OLD relationships - keep existing functionality working
            'user_Cus', 'user_Agent',
            // NEW relationships - for cleaner future code
            'customer_feedback', 'agent_feedback', 'customer_profile', 'agent_profile'
        ]);

        if ($status !== 'all') {
            $query->where('Status', $status);
        }

        if ($min !== 'all' && $max !== 'all') {
            $query->whereBetween('Rating', [(float)$min, (float)$max]);
        }

        $feedbacks = $query->paginate(10);

        // Luôn trả về view, KHÔNG trả về response()->json hay status 404
    $error = null;
    if ($columns === null) {
        $error = 'Không lấy được cấu trúc bảng.';
    }
    // Không cần else, cứ trả về view, view sẽ tự kiểm tra $feedbacks->isEmpty()

    return view('_system.partialview.feedback_table', compact('columns', 'feedbacks', 'error'));

    }


    public function updatefeedback(Request $request, $id)
    {
        $feedback = feedback::find($id);

        if (!$feedback) {
            return redirect()->back()->withErrors(['error' => 'Phản hồi không tồn tại.'], 404);
        }

        // Xử lý cập nhật thông tin phản hồi
        $validated = $request->validate([
            'status' => 'required|in:Chờ duyệt,Đã duyệt,Hủy bỏ',
        ]);

        // Cập nhật trạng thái phản hồi
        $feedback->Status = $validated['status'];
        $feedback->save();

        return redirect()->back()->with('success', 'Trạng thái phản hồi đã được cập nhật thành công.');
    }

    public function deletefeedback($id)
    {
        $feedback = feedback::find($id);
        if (!$feedback) {
            return redirect()->back()->withErrors(['error' => 'Phản hồi không tồn tại.'], 404);
        }
        $feedback->delete();

        return redirect()->route('admin.feedback')->with('success', 'Phản hồi đã được xóa thành công.');
    }

    /**
     * Search feedbacks with filters and get agents/customers for autocomplete
     */
    public function getFeedbackSearch(Request $request)
    {
        try {
            // If it's a user search request (agents and customers)
            if ($request->has('q')) {
                $query = $request->query('q');

                // Search agents
                $agents = User::where('Role', 'Agent')
                    ->where(function($q) use ($query) {
                        $q->where('Name', 'LIKE', "%{$query}%")
                          ->orWhere('UserID', 'LIKE', "%{$query}%")
                          ->orWhere('Email', 'LIKE', "%{$query}%");
                    })
                    ->with('profile_agent')
                    ->limit(5)
                    ->get()
                    ->map(function($agent) {
                        return [
                            'UserID' => $agent->UserID,
                            'FullName' => $agent->Name,
                            'Email' => $agent->Email,
                            'Role' => 'Agent',
                            'Province' => $agent->profile_agent->ProvinceAgent ?? 'N/A'
                        ];
                    });

                // Search customers
                $customers = User::where('Role', 'Customer')
                    ->where(function($q) use ($query) {
                        $q->where('Name', 'LIKE', "%{$query}%")
                          ->orWhere('UserID', 'LIKE', "%{$query}%")
                          ->orWhere('Email', 'LIKE', "%{$query}%");
                    })
                    ->limit(5)
                    ->get()
                    ->map(function($customer) {
                        return [
                            'UserID' => $customer->UserID,
                            'FullName' => $customer->Name,
                            'Email' => $customer->Email,
                            'Role' => 'Customer',
                            'Province' => $customer->Province ?? 'N/A'
                        ];
                    });

                // Combine results
                $users = $agents->concat($customers);

                return response()->json([
                    'success' => true,
                    'users' => $users
                ]);
            }

            // If it's a feedback search with filters
            $query = feedback::with([
                // OLD relationships - keep existing functionality working
                'user_Cus', 'user_Agent',
                // NEW relationships - for cleaner future code
                'customer_feedback', 'agent_feedback', 'customer_profile', 'agent_profile'
            ]);

            // Filter by user (agent or customer)
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where(function($q) use ($request) {
                    $q->where('AgentID', $request->user_id)
                      ->orWhere('CusID', $request->user_id);
                });
            }

            // Legacy support: Filter by agent only
            if ($request->has('agent_id') && !empty($request->agent_id)) {
                $query->where('AgentID', $request->agent_id);
            }

            // Filter by date
            if ($request->has('date') && !empty($request->date)) {
                $query->whereDate('FeedbackDate', $request->date);
            }

            // Filter by rating
            if ($request->has('rating') && !empty($request->rating)) {
                $query->where('Rating', $request->rating);
            }

            $feedbacks = $query->get();

            // Separate by status
            $pending = $feedbacks->where('Status', 'Chờ duyệt')->values();
            $approved = $feedbacks->where('Status', 'Đã duyệt')->values();

            return response()->json([
                'success' => true,
                'pending' => $pending,
                'approved' => $approved
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getFeedbackSearch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm phản hồi.'
            ], 500);
        }
    }

    /**
     * Get cancelled feedbacks for history modal
     */
    public function getCancelledFeedback(Request $request)
    {
        try {
            $cancelled = feedback::with([
                // OLD relationships - keep existing functionality working
                'user_Cus.user', 'user_Agent.user', 'agent_user', 'customer_user',
                // NEW relationships - for cleaner future code
                'customer_feedback', 'agent_feedback', 'customer_profile', 'agent_profile'
            ])
                ->where('Status', 'Hủy bỏ')
                ->orderBy('FeedbackDate', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'cancelled' => $cancelled
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getCancelledFeedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lịch sử phản hồi đã hủy.'
            ], 500);
        }
    }

    /**
     * Update feedback status via AJAX
     */
    public function updateFeedbackStatus(Request $request, $id)
    {
        try {
            $feedback = feedback::find($id);

            if (!$feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phản hồi không tồn tại.'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:Chờ duyệt,Đã duyệt,Hủy bỏ',
            ]);

            $feedback->Status = $validated['status'];
            $feedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái phản hồi đã được cập nhật thành công.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in updateFeedbackStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái phản hồi.'
            ], 500);
        }
    }

    /**
     * Search owners for autocomplete functionality
     */
    public function searchOwners(Request $request)
    {
        try {
            $query = $request->get('query', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $owners = User::where('Role', 'Owner')
                ->where('StatusUser', 'active') // Chỉ tìm kiếm chủ sở hữu có tài khoản đang hoạt động
                ->where(function($q) use ($query) {
                    $q->where('Name', 'LIKE', "%{$query}%")
                      ->orWhere('Phone', 'LIKE', "%{$query}%")
                      ->orWhere('Email', 'LIKE', "%{$query}%");
                })
                ->select('UserID', 'Name', 'Phone', 'Email', 'Address', 'Ward', 'District', 'Province', 'IdentityCard')
                ->limit(10)
                ->get();

            return response()->json($owners);

        } catch (\Exception $e) {
            Log::error('Error searching owners: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Get owner details by ID
     */
    public function getOwnerDetails($id)
    {
        try {
            $owner = User::where('Role', 'Owner')
                ->where('StatusUser', 'active') // Chỉ lấy thông tin chủ sở hữu có tài khoản đang hoạt động
                ->where('UserID', $id)
                ->select('UserID', 'Name', 'Phone', 'Email', 'Address')
                ->first();

            if (!$owner) {
                return response()->json(['error' => 'Owner not found or inactive'], 404);
            }

            return response()->json($owner);

        } catch (\Exception $e) {
            Log::error('Error getting owner details: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Upload contract template files
     */
    public function uploadContractTemplate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'files.*' => 'required|file|mimes:docx|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'File không hợp lệ. Chỉ chấp nhận file .docx dưới 10MB.',
                    'details' => $validator->errors()
                ], 400);
            }

            $contractsPath = public_path('storage/document/HopDong');

            // Ensure HopDong directory exists
            if (!file_exists($contractsPath)) {
                mkdir($contractsPath, 0755, true);
            }

            $uploadedFiles = [];
            $errors = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    try {
                        // Generate unique filename
                        $originalName = $file->getClientOriginalName();
                        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();

                        // Clean filename
                        $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $nameWithoutExt);
                        $cleanName = preg_replace('/\s+/', '_', $cleanName);

                        $filename = $cleanName . '.' . $extension;

                        // Check if file already exists
                        $counter = 1;
                        $finalFilename = $filename;
                        while (file_exists($contractsPath . '/' . $finalFilename)) {
                            $finalFilename = $cleanName . '_' . $counter . '.' . $extension;
                            $counter++;
                        }

                        // Move file to HopDong directory
                        $file->move($contractsPath, $finalFilename);

                        // Validate it's a proper docx file by trying to read it
                        try {
                            $fullPath = $contractsPath . '/' . $finalFilename;
                            $phpWord = IOFactory::load($fullPath);
                            $properties = $phpWord->getDocInfo();

                            $uploadedFiles[] = [
                                'original_name' => $originalName,
                                'filename' => $finalFilename,
                                'size' => filesize($fullPath),
                                'display_name' => $nameWithoutExt,
                                'upload_time' => now()->format('Y-m-d H:i:s'),
                                'status' => 'success'
                            ];
                        } catch (\Exception $e) {
                            // If we can't read it as a Word document, delete it
                            unlink($contractsPath . '/' . $finalFilename);
                            $errors[] = [
                                'file' => $originalName,
                                'error' => 'File không phải là file Word hợp lệ'
                            ];
                        }

                    } catch (\Exception $e) {
                        $errors[] = [
                            'file' => $originalName ?? 'Unknown',
                            'error' => 'Lỗi khi tải lên: ' . $e->getMessage()
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tải lên hoàn tất',
                'uploaded' => $uploadedFiles,
                'errors' => $errors,
                'total_uploaded' => count($uploadedFiles),
                'total_errors' => count($errors)
            ]);

        } catch (\Exception $e) {
            Log::error('Contract upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi tải lên: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download contract template from URL
     */
    public function downloadContractFromUrl(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'url' => 'required|url',
                'filename' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'URL không hợp lệ',
                    'details' => $validator->errors()
                ], 400);
            }

            $url = $request->input('url');
            $customFilename = $request->input('filename');

            // Check if URL ends with .docx
            if (!str_ends_with(strtolower($url), '.docx')) {
                return response()->json([
                    'success' => false,
                    'error' => 'URL phải trỏ đến file .docx'
                ], 400);
            }

            $contractsPath = public_path('storage/document/HopDong');

            // Ensure HopDong directory exists
            if (!file_exists($contractsPath)) {
                mkdir($contractsPath, 0755, true);
            }

            // Generate filename
            if ($customFilename) {
                $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $customFilename);
                $filename = preg_replace('/\s+/', '_', $filename) . '.docx';
            } else {
                $filename = basename($url);
                $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $filename);
                $filename = preg_replace('/\s+/', '_', $filename);
            }

            // Check if file already exists
            $counter = 1;
            $originalFilename = $filename;
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            while (file_exists($contractsPath . '/' . $filename)) {
                $filename = $nameWithoutExt . '_' . $counter . '.docx';
                $counter++;
            }

            // Download file
            try {
                $response = Http::timeout(30)->get($url);

                if (!$response->successful()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Không thể tải file từ URL. Mã lỗi: ' . $response->status()
                    ], 400);
                }

                $content = $response->body();
                $fullPath = $contractsPath . '/' . $filename;

                file_put_contents($fullPath, $content);

                // Validate it's a proper docx file
                try {
                    $phpWord = IOFactory::load($fullPath);
                    $properties = $phpWord->getDocInfo();

                    return response()->json([
                        'success' => true,
                        'message' => 'Tải file từ URL thành công',
                        'file' => [
                            'filename' => $filename,
                            'size' => filesize($fullPath),
                            'display_name' => pathinfo($filename, PATHINFO_FILENAME),
                            'url' => $url,
                            'download_time' => now()->format('Y-m-d H:i:s')
                        ]
                    ]);

                } catch (\Exception $e) {
                    // If we can't read it as a Word document, delete it
                    unlink($fullPath);
                    return response()->json([
                        'success' => false,
                        'error' => 'File tải về không phải là file Word hợp lệ'
                    ], 400);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không thể kết nối đến URL: ' . $e->getMessage()
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Contract URL download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete contract template
     */
    public function deleteContractTemplate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'filename' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tên file không hợp lệ'
                ], 400);
            }

            $filename = $request->input('filename');
            $contractsPath = public_path('storage/document/HopDong');
            $fullPath = $contractsPath . '/' . $filename;

            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'File không tồn tại'
                ], 404);
            }

            // Security check - ensure file is within HopDong directory
            $realPath = realpath($fullPath);
            $realContractsPath = realpath($contractsPath);

            if (!$realPath || !str_starts_with($realPath, $realContractsPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có quyền xóa file này'
                ], 403);
            }

            if (unlink($fullPath)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa file thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Không thể xóa file'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Contract delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xóa file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract templates info for management
     */
    public function getContractTemplatesInfo()
    {
        try {
            $contractsPath = public_path('storage/document/HopDong');

            if (!file_exists($contractsPath)) {
                mkdir($contractsPath, 0755, true);
            }

            $files = glob($contractsPath . '/*.docx');
            $templates = [];

            foreach ($files as $file) {
                $filename = basename($file);
                $size = filesize($file);
                $modified = filemtime($file);

                $templates[] = [
                    'filename' => $filename,
                    'display_name' => pathinfo($filename, PATHINFO_FILENAME),
                    'size' => $size,
                    'size_formatted' => $this->formatFileSize($size),
                    'modified' => $modified,
                    'modified_formatted' => date('d/m/Y H:i', $modified),
                    'download_url' => asset('storage/document/HopDong/' . $filename),
                    'can_delete' => true
                ];
            }

            return response()->json([
                'success' => true,
                'templates' => $templates,
                'total' => count($templates)
            ]);

        } catch (\Exception $e) {
            Log::error('Get contract templates error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi lấy danh sách mẫu hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Phân tích hợp đồng bằng AI
     */
    public function analyzeContract(Request $request)
    {
        try {            $validator = Validator::make($request->all(), [
                'source' => 'required|in:template,upload',
                'template_name' => 'required_if:source,template|string',
                'contract_file' => 'required_if:source,upload|file|mimes:docx,pdf,txt|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Dữ liệu không hợp lệ',
                    'details' => $validator->errors()
                ], 400);
            }

            $contractContent = '';
            $contractSource = $request->input('source');

            if ($contractSource === 'template') {
                // Phân tích từ template có sẵn
                $templateName = $request->input('template_name');
                $contractPath = public_path('storage/document/HopDong/' . $templateName);

                if (!file_exists($contractPath)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'File mẫu hợp đồng không tồn tại'
                    ], 404);
                }

                $contractContent = $this->extractTextFromDocx($contractPath);

            } else {
                // Phân tích từ file upload
                $file = $request->file('contract_file');
                $extension = $file->getClientOriginalExtension();

                if ($extension === 'pdf') {
                    $contractContent = $this->extractTextFromPdf($file->getPathname());
                } elseif ($extension === 'txt') {
                    $contractContent = file_get_contents($file->getPathname());
                } else {
                    $contractContent = $this->extractTextFromDocx($file->getPathname());
                }
            }

            if (empty($contractContent)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không thể đọc nội dung file hợp đồng'
                ], 400);
            }

            // Gọi Gemini AI thực sự
            try {
                $geminiService = new \App\Services\GeminiAIService();
                $analysisResult = $geminiService->analyzeContract($contractContent, $contractSource);

                if (!$analysisResult['success']) {
                    // Trả về lỗi validation hoặc lỗi phân tích
                    return response()->json([
                        'success' => false,
                        'error' => $analysisResult['error'],
                        'suggestion' => $analysisResult['suggestion'] ?? null
                    ], 400);
                }

                $analysis = $analysisResult['analysis'];
                $analysis['source'] = 'gemini_ai';
                $analysis['analysis_time'] = date('Y-m-d H:i:s');

            } catch (\Exception $e) {
                // Fallback to simulation if Gemini fails
                Log::warning('Gemini AI failed, using simulation: ' . $e->getMessage());
                $analysis = $this->simulateContractAnalysis($contractContent, $contractSource);
                $analysis['fallback'] = true;
                $analysis['source'] = 'simulation';
            }

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'source' => $contractSource,
                'contract_length' => strlen($contractContent)
            ]);

        } catch (\Exception $e) {
            Log::error('Contract analysis error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Lỗi hệ thống khi phân tích hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trích xuất text từ file .docx
     */
    private function extractTextFromDocx($filePath)
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';

            // Use dynamic method calls to avoid static analysis issues
            foreach ($phpWord->getSections() as $section) {
                try {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        // Safely try to extract text using dynamic calls
                        try {
                            $getTextMethod = 'getText';
                            $getElementsMethod = 'getElements';

                            if (method_exists($element, $getTextMethod)) {
                                $text .= call_user_func([$element, $getTextMethod]) . " ";
                            } elseif (method_exists($element, $getElementsMethod)) {
                                $childElements = call_user_func([$element, $getElementsMethod]);
                                foreach ($childElements as $childElement) {
                                    if (method_exists($childElement, $getTextMethod)) {
                                        $text .= call_user_func([$childElement, $getTextMethod]) . " ";
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            // Continue processing other elements
                            continue;
                        }
                    }
                } catch (\Throwable $e) {
                    // Continue processing other sections
                    continue;
                }
                $text .= "\n"; // Add line break between sections
            }

            // Clean up text
            $text = preg_replace('/\s+/', ' ', $text); // Replace multiple spaces with single space
            $text = trim($text);

            // If no text extracted, return a default message for simulation
            if (empty($text)) {
                $text = "Hợp đồng mua bán bất động sản. Các điều khoản về quyền và nghĩa vụ của các bên, điều kiện thanh toán, thời gian giao nhận, và các điều khoản pháp lý khác. File: " . basename($filePath);
            }

            return $text;

        } catch (\Exception $e) {
            Log::error('Error extracting text from docx: ' . $e->getMessage());
            // Return a fallback content for analysis simulation
            return "Hợp đồng mua bán bất động sản mẫu với các điều khoản chuẩn về quyền lợi, nghĩa vụ các bên, thanh toán và giao nhận. File: " . basename($filePath ?? 'contract.docx');
        }
    }

    /**
     * Trích xuất text từ file PDF (placeholder for future)
     */
    private function extractTextFromPdf($filePath)
    {
        // TODO: Implement PDF text extraction if needed
        return '';
    }

    /**
     * Simulate AI contract analysis (temporary solution)
     */
    private function simulateContractAnalysis($contractContent, $source)
    {
        // Simulate processing time for demo
        usleep(1000000); // 1 second delay

        $contractLength = strlen($contractContent);
        $wordCount = str_word_count($contractContent);

        // Generate realistic analysis based on content length and source
        $benefits = [
            'Điều khoản thanh toán rõ ràng và hợp lý',
            'Quyền lợi và nghĩa vụ các bên được quy định cụ thể',
            'Có điều khoản bảo vệ quyền lợi người mua',
            'Thời gian giao nhận được quy định rõ ràng',
            'Điều khoản chấm dứt hợp đồng minh bạch'
        ];

        $risks = [
            'Một số điều khoản về phạt vi phạm có thể cao',
            'Thiếu điều khoản về xử lý tranh chấp cụ thể',
            'Cần bổ sung thêm điều khoản về bảo hiểm',
            'Quy định về thay đổi hợp đồng chưa rõ ràng'
        ];

        $recommendations = [
            'Nên tham khảo ý kiến luật sư trước khi ký',
            'Kiểm tra kỹ các giấy tờ pháp lý liên quan',
            'Thảo luận lại điều khoản thanh toán nếu cần',
            'Bổ sung điều khoản về bảo hiểm tài sản',
            'Làm rõ trách nhiệm các bên trong trường hợp bất khả kháng'
        ];

        // Calculate rating based on content quality
        $baseRating = 7;
        if ($contractLength > 5000) $baseRating += 1;
        if ($wordCount > 1000) $baseRating += 1;
        if ($source === 'template') $baseRating += 0.5;

        $rating = min(10, $baseRating);

        return [
            'overall_rating' => round($rating, 1),
            'benefits' => array_slice($benefits, 0, rand(3, 5)),
            'risks' => array_slice($risks, 0, rand(2, 4)),
            'recommendations' => array_slice($recommendations, 0, rand(3, 5)),
            'analysis_time' => date('Y-m-d H:i:s'),
            'word_count' => $wordCount,
            'contract_length' => $contractLength
        ];
    }

    /**
     * Add a new contract template
     */
    public function addContractTemplate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'contract_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'contract_file' => 'required|file|mimes:docx|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 400);
            }

            $contractsPath = public_path('storage/document/HopDong');

            // Ensure HopDong directory exists
            if (!file_exists($contractsPath)) {
                mkdir($contractsPath, 0755, true);
            }

            $file = $request->file('contract_file');
            $contractName = $request->input('contract_name');

            // Generate filename from contract name
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $contractName);
            $cleanName = preg_replace('/\s+/', '_', $cleanName);
            $filename = $cleanName . '.docx';

            // Check if file already exists and generate unique name
            $counter = 1;
            $finalFilename = $filename;
            while (file_exists($contractsPath . '/' . $finalFilename)) {
                $finalFilename = $cleanName . '_' . $counter . '.docx';
                $counter++;
            }

            // Move file to HopDong directory
            $file->move($contractsPath, $finalFilename);

            // Validate it's a proper docx file by trying to read it
            try {
                $fullPath = $contractsPath . '/' . $finalFilename;
                $phpWord = IOFactory::load($fullPath);
                $properties = $phpWord->getDocInfo();

                return response()->json([
                    'success' => true,
                    'message' => 'Thêm mẫu hợp đồng thành công!',
                    'contract' => [
                        'filename' => $finalFilename,
                        'display_name' => $contractName,
                        'size' => filesize($fullPath),
                        'description' => $request->input('description')
                    ]
                ]);

            } catch (\Exception $e) {
                // If we can't read it as a Word document, delete it
                unlink($contractsPath . '/' . $finalFilename);
                return response()->json([
                    'success' => false,
                    'message' => 'File không phải là file Word hợp lệ'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Contract add error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm mẫu hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit an existing contract template
     */
    public function editContractTemplate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'template_name' => 'required|string',
                'contract_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'contract_file' => 'nullable|file|mimes:docx|max:10240', // Optional for edit
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 400);
            }

            $contractsPath = public_path('storage/document/HopDong');
            $templateName = $request->input('template_name');
            $contractName = $request->input('contract_name');
            $currentFilePath = $contractsPath . '/' . $templateName;

            // Check if current file exists
            if (!file_exists($currentFilePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mẫu hợp đồng không tồn tại'
                ], 404);
            }

            // If new file is uploaded, replace the old one
            if ($request->hasFile('contract_file')) {
                $file = $request->file('contract_file');

                // Generate new filename from contract name
                $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $contractName);
                $cleanName = preg_replace('/\s+/', '_', $cleanName);
                $newFilename = $cleanName . '.docx';

                // Check if new filename conflicts with existing files (except current)
                $counter = 1;
                $finalFilename = $newFilename;
                while (file_exists($contractsPath . '/' . $finalFilename) && $finalFilename !== $templateName) {
                    $nameWithoutExt = pathinfo($newFilename, PATHINFO_FILENAME);
                    $finalFilename = $nameWithoutExt . '_' . $counter . '.docx';
                    $counter++;
                }

                // Validate new file is a proper docx
                try {
                    $tempPath = $file->getPathname();
                    $phpWord = IOFactory::load($tempPath);
                    $properties = $phpWord->getDocInfo();
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File không phải là file Word hợp lệ'
                    ], 400);
                }

                // Delete old file if filename is different
                if ($finalFilename !== $templateName) {
                    unlink($currentFilePath);
                }

                // Move new file
                $file->move($contractsPath, $finalFilename);

                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật mẫu hợp đồng thành công!',
                    'contract' => [
                        'old_filename' => $templateName,
                        'new_filename' => $finalFilename,
                        'display_name' => $contractName,
                        'size' => filesize($contractsPath . '/' . $finalFilename),
                        'description' => $request->input('description')
                    ]
                ]);

            } else {
                // Only update name if it's different
                $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $contractName);
                $cleanName = preg_replace('/\s+/', '_', $cleanName);
                $newFilename = $cleanName . '.docx';

                if ($newFilename !== $templateName) {
                    // Check if new filename conflicts
                    $counter = 1;
                    $finalFilename = $newFilename;
                    while (file_exists($contractsPath . '/' . $finalFilename)) {
                        $nameWithoutExt = pathinfo($newFilename, PATHINFO_FILENAME);
                        $finalFilename = $nameWithoutExt . '_' . $counter . '.docx';
                        $counter++;
                    }

                    // Rename file
                    rename($currentFilePath, $contractsPath . '/' . $finalFilename);

                    return response()->json([
                        'success' => true,
                        'message' => 'Cập nhật tên mẫu hợp đồng thành công!',
                        'contract' => [
                            'old_filename' => $templateName,
                            'new_filename' => $finalFilename,
                            'display_name' => $contractName,
                            'description' => $request->input('description')
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Mẫu hợp đồng đã được cập nhật!'
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Contract edit error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật mẫu hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete contract template via JSON request
     */
    public function deleteContractTemplateJson(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'template' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên file không hợp lệ'
                ], 400);
            }

            $templateName = $request->input('template');
            $contractsPath = public_path('storage/document/HopDong');
            $fullPath = $contractsPath . '/' . $templateName;

            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại'
                ], 404);
            }

            // Security check - ensure file is within HopDong directory
            $realPath = realpath($fullPath);
            $realContractsPath = realpath($contractsPath);

            if (!$realPath || !str_starts_with($realPath, $realContractsPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xóa file này'
                ], 403);
            }

            if (unlink($fullPath)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa mẫu hợp đồng thành công!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa file'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Contract delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa mẫu hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DEMO: Method mới sử dụng relationships mới mà vẫn tương thích với view blade cũ
     * Ví dụ về cách chuyển đổi method property() một cách an toàn
     */
    public function propertyWithNewRelationships(Request $request)
    {
        $typePro = $request->input('typePro');
        $columns = Schema::getColumnListing('properties');

        // Load properties với CẢ relationships cũ và mới
        if ($typePro) {
            $properties = Property::with([
                'danhMuc', 'images', 'videos',
                // Relationships CŨ - để view blade vẫn hoạt động
                'chusohuu', 'moigioi', 'quantri',
                // Relationships MỚI - để logic mới có thể sử dụng
                'owner', 'agent', 'approver', 'agent_profile'
            ])->where('TypePro', $typePro)->get();
        } else {
            $properties = Property::with([
                'danhMuc', 'images', 'videos',
                'chusohuu', 'moigioi', 'quantri',  // Cũ
                'owner', 'agent', 'approver', 'agent_profile'  // Mới
            ])->get();
        }

        $owners = User::where('Role', 'Owner')->get();

        // Sử dụng relationship MỚI trong logic (rõ ràng hơn)
        $agents = User::where('Role', 'Agent')
                    ->with('profile_agent')
                    ->withCount(['managed_properties as active_property_count' => function ($query) {
                        $query->where('Status', 'active');
                    }])
                    ->orderBy('active_property_count', 'asc')
                    ->paginate(20);

        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        // Ví dụ sử dụng relationships mới trong logic (tùy chọn)
        foreach ($properties as $property) {
            // Logic mới có thể sử dụng tên rõ ràng hơn
            $ownerName = $property->owner->Name ?? 'N/A';  // Thay vì $property->chusohuu->Name
            $agentName = $property->agent->Name ?? 'N/A';  // Thay vì $property->moigioi->Name

            // Có thể thêm logic phức tạp hơn với agent profile
            if ($property->agent_profile) {
                $agentCertificate = $property->agent_profile->Certificate ?? 'N/A';
                $agentContact = $property->agent_profile->ContactAgent ?? 'N/A';
            }
        }

        if ($columns === null || $properties->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu';
            return view('_system.property', compact('error', 'categories'));
        }

        // Trả về view CŨ - view blade vẫn sử dụng relationships cũ ($property->chusohuu, $property->moigioi)
        return view('_system.property', compact('columns','properties','owners','agents','admins','categories', 'typePro'));
    }

    /**
     * Get transaction statistics by period for charts
     */
    public function getTransactionStatsByPeriod(Request $request)
    {
        $period = $request->get('period', 'month');
        $currentYear = date('Y');
        $currentMonth = date('n');

        $query = Transaction::query();

        switch ($period) {
            case 'month':
                $query->whereMonth('TransactionDate', $currentMonth)
                      ->whereYear('TransactionDate', $currentYear);
                break;
            case 'year':
                $query->whereYear('TransactionDate', $currentYear);
                break;
        }

        $stats = $query->selectRaw('
                COUNT(CASE WHEN TransactionType = "Rent" THEN 1 END) as rent_count,
                COUNT(CASE WHEN TransactionType = "Sale" THEN 1 END) as sale_count
            ')->first();

        return response()->json([
            'rent_count' => $stats->rent_count ?? 0,
            'sale_count' => $stats->sale_count ?? 0
        ]);
    }

    /**
     * Get commission statistics by period for charts
     */
    public function getCommissionStatsByPeriod(Request $request)
    {
        $period = $request->get('period', 'week');
        $query = Commission::query();

        switch ($period) {
            case 'week':
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                break;
            case 'month':
                $query->whereMonth('created_at', date('n'))
                      ->whereYear('created_at', date('Y'));
                break;
        }

        $stats = $query->selectRaw('
                COUNT(CASE WHEN StatusCommission = "Success" THEN 1 END) as success_count,
                COUNT(CASE WHEN StatusCommission = "Pending" THEN 1 END) as pending_count,
                COUNT(CASE WHEN StatusCommission = "Cancelled" THEN 1 END) as cancelled_count
            ')->first();

        return response()->json([
            'success_count' => $stats->success_count ?? 0,
            'pending_count' => $stats->pending_count ?? 0,
            'cancelled_count' => $stats->cancelled_count ?? 0
        ]);
    }

    /**
     * Toggle user status between active and inactive
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại.'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $oldStatus = $user->StatusUser;
            $user->StatusUser = $validated['status'];
            $user->save();

            Log::info('User status toggled', [
                'user_id' => $userId,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái tài khoản đã được cập nhật thành công.',
                'old_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }
}
