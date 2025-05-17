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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    //
    public function admin()
    {
        if (Auth::check()) {
            return view('_system.index');
        } else {
            abort(403, 'Bạn không có quyền truy cập vào trang này.');
        }
    }

    public function getUser()
    {
        $columns = Schema::getColumnListing('user');
        $users = User::paginate(10); // Sửa $user thành $users
        if ($columns === null || $users->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.users', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.users', compact('columns','users')); // Đảm bảo biến truyền vào view là $users
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
            return view('_system.partialview.user_table', compact('columns','users')); // Đảm bảo biến truyền vào view là $users
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
            // 'identity_card' => 'required|max:12|regex:/^(\d{9}|\d{12})$/',
            // 'phone' => 'required|max:10|regex:/^(\d{10})$/',
            'identity_card' => 'required|max:12',
            'phone' => 'required|max:10',
            'address' => 'required|max:255',
            'ward' => 'required|max:255',
            'district' => 'required|max:255',
            'province' => 'required|max:255',
            'role' => 'required',
            'password' => 'required|min:6|max:30',
        ]);

        $userExists = User::where('Email', $validated['email'])->first();
        if ($userExists) {
            return redirect()->back()->withErrors('error','Email đã tồn tại.');
        }

        User::create([
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
            'StatusUser' => 'active',
            'PasswordHash' => $validated['password'],
        ]);

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
            'PasswordHash' => $validated['password'],
        ];

        // Nếu có nhập password mới thì cập nhật
        if (empty($validated['password'])) {
           $user->password  = $validated['password']; // Giữ nguyên mật khẩu cũ
        }


        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    public function DeleteUser($id)
    {
        // $user = User::find($id);
        // if (!$user) {
        //     return redirect()->back()->withErrors(['error' => 'Người dùng không tồn tại.'], 404);
        // }
        // $user->delete();

        try{
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

    public function SearchUser(Request $request)
    {
        $keyword = $request->input('keyword');


        // Thực hiện tìm kiếm trực tiếp trong bảng user
        $users = DB::table('user')
        ->select('*')
        ->where(function ($query) use ($keyword) {
            $query->whereRaw('LOWER(UserID) LIKE ?', ['%' . strtolower($keyword) . '%'])
                  ->orWhereRaw('LOWER(Name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                  ->orWhereRaw('LOWER(Email) LIKE ?', ['%' . strtolower($keyword) . '%'])
                  ->orWhereRaw('LOWER(Phone) LIKE ?', ['%' . strtolower($keyword) . '%']);
        })
        ->paginate(12);



        // Kiểm tra nếu không có dữ liệu
        if (empty($users)) {
            return view('_system.users', [
                'users' => [],
                'columns' => Schema::getColumnListing('user'), // Lấy tất cả các cột từ bảng
                'error' => 'Không tìm thấy user nào.'
            ]);
        }

        // Trả về view bảng user
        return view('_system.users', [
            'users' => $users,
            'columns' => Schema::getColumnListing('user') // Lấy tất cả các cột từ bảng
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

    public function getProperty()
    {
        $columns = Schema::getColumnListing('properties');
        $properties = Property::all();
        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        if ($columns === null || $properties->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.property', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.property', compact('columns','properties','owners','agents','admins','categories')); // Đảm bảo biến truyền vào view là $users
    }

    public function getPropertyByType(Request $request, $type)
    {
        $columns = Schema::getColumnListing('properties');

        if ($type == 'all') {
            $properties = Property::with(['danhMuc'])->paginate(10); // Sửa pageinate thành paginate
        } else {
            $properties = Property::with(['danhMuc'])
                ->where('TypePro', $type)
                ->paginate(10);
        }

        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();
        if ($columns === null || $properties->isEmpty()) {
            return view('_system.partialview.property_table', compact('error'));
        } else {
            return view('_system.partialview.property_table', compact('columns', 'properties', 'owners', 'agents', 'admins', 'categories'));
        }
    }

    public function getPropertyByStatus(Request $request, $status)
    {
        $columns = Schema::getColumnListing('properties');

        if ($status == 'all') {
            $properties = Property::with(['danhMuc'])->paginate(10); // Sửa pageinate thành paginate
        } else {
            $properties = Property::with(['danhMuc'])
                ->where('Status', $status)
                ->paginate(10);
        }

        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->get();
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
        $agents = User::where('Role', 'Agent')->get();
        $admins = User::where('Role', 'Admin')->get();
        $categories = DanhMucBDS::all();

        $properties = Property::with(['danhMuc', 'chusohuu', 'moigioi', 'quantri'])
                    ->whereRaw('LOWER(Title) LIKE ?', ['%' . strtolower($keyword) . '%'])
                    ->paginate(10);

        if ($columns === null || $properties->isEmpty()) {
            return view('_system.property', compact('error'));
        }

        return view('_system.property', compact('columns', 'properties', 'owners', 'agents', 'admins', 'categories'));
    }

    public function getPropertyById($id)
    {
        $property = Property::with(['chitiet'])->find($id);
        if (!$property) {
            return redirect()->back()->withErrors(['error' => 'Bất động sản không tồn tại.'], 404);
        }
        $columns = Schema::getColumnListing('properties');
        $owners = User::where('Role', 'Owner')->get();
        $agents = User::where('Role', 'Agent')->get();
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

    public function createPropertyForm()
    {
        $provinces = $this->fetchProvinces();
        return view('_system.partialview.create_property', compact('provinces'));
    }

    //
    // Phần này của AssignProperty
    //

    // Hàm function này lấy danh sách User và Profile_Agent của người dùng
    public function getAssignProperty()
    {
        // Get columns from both user and profile_agent tables
        // Lấy danh sách agent
        $agents = User::where('Role', 'Agent')->get();

        // Lấy danh sách bất động sản chưa có agent hoặc đang cần phân công lại
        foreach ($agents as $agent) {
        $agent->activePropertyCount = Property::where('AgentID', $agent->UserID)
                                    ->where('Status', 'active')
                                    ->count();
        }

        // Lấy danh sách bất động sản chưa có agent hoặc đang cần phân công lại
        $properties = Property::with(['chusohuu', 'moigioi'])
                    ->paginate(10);

        // Trả về view với dữ liệu
        return view('_system.partialview.assign_property', compact('agents', 'properties'));
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
        $appointments = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property'])->paginate(10);
        if ($columns === null || $appointments->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.appointment', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.appointment', compact('columns','appointments')); // Đảm bảo biến truyền vào view là $users
    }

    public function getAppointmentById(Request $request, $id)
    {
        $columns = Schema::getColumnListing('appointments');
        $appointment = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property'])->find($id)->paginate(10);
        if ($columns === null || $appointment === null) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.appointment', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.partialview.checkedit_appoint', compact('columns','appointment')); // Đảm bảo biến truyền vào view là $users
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
        $columns = Schema::getColumnListing('appointments');
        $appointments = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property'])
                        ->whereDate('AppointmentDateStart', $date)
                        ->get();

        if ($columns === null || $appointments->isEmpty()) {
            return view('_system.appointment', ['error' => 'Không tìm thấy cuộc hẹn nào vào ngày ' . $date]);
        }

        return view('_system.appointment', compact('columns', 'appointments'));
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

        return view('_system.transaction', compact('columns','transactions')); // Đảm bảo biến truyền vào view là $users
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
    // Phần này của feedback
    //

    public function getFeedback()
    {
        $columns = Schema::getColumnListing('feedbacks');
        $feedbacks = feedback::with(['user_Cus', 'user_Agent'])->paginate(10);
        if ($columns === null || $feedbacks->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.feedback', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.feedback', compact('columns','feedbacks')); // Đảm bảo biến truyền vào view là $users
    }

    // Hàm lọc phản hồi theo trạng thái và số sao

    public function getFeedbackByStatusRating(Request $request)
    {
        $columns = Schema::getColumnListing('feedbacks');
        $status = $request->query('status', 'all');
        $min = $request->query('min', 'all');
        $max = $request->query('max', 'all');

        $query = feedback::with(['user_Cus', 'user_Agent']);

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

    //
    // Phần này của commission
    //

    public function getCommission()
    {
        $columns = Schema::getColumnListing('commission');
        $commissions = Commission::all();
        if ($columns === null || $commissions->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.commission', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.commission', compact('columns','commissions')); // Đảm bảo biến truyền vào view là $users
    }


    private function fetchProvinces()
    {
        // Gọi API để lấy danh sách tỉnh
        $response = Http::get('https://provinces.open-api.vn/api/?depth=2');
        return $response->json();
    }

    public function viewDocument($id)
    {
        // Kiểm tra xác thực người dùng
        if (!Auth::check()) {
            abort(403, 'Vui lòng đăng nhập để truy cập tài liệu.');
        }

        // Tìm tài liệu
        $document = Document::with(['transaction'])->find($id);

        if (!$document) {
            return back()->with('error', 'Không tìm thấy tài liệu');
        }

        // Kiểm tra quyền truy cập
        $user = Auth::user();
        $transaction = $document->transaction;

        // Chỉ cho phép admin, agent liên quan, chủ sở hữu và khách hàng liên quan đến giao dịch này truy cập
        $hasAccess = false;

        if ($user->Role == 'Admin') {
            $hasAccess = true;
        } elseif ($user->Role == 'Agent' && $transaction->AgentID == $user->UserID) {
            $hasAccess = true;
        } elseif ($user->Role == 'Owner' && $transaction->OwnerID == $user->UserID) {
            $hasAccess = true;
        } elseif ($user->Role == 'Customer' && $transaction->CusID == $user->UserID) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền xem tài liệu này.');
        }

        // Ghi log truy cập
        Log::info('User ' . $user->UserID . ' accessed document ' . $id . ' at ' . now());

        // Sử dụng đường dẫn private trong storage thay vì public
        $filePath = storage_path('app/' .$document->FilePath);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Tài liệu không tồn tại trên hệ thống');
        }

        // Trả về file thông qua response để tránh truy cập trực tiếp
        return response()->file($filePath);
    }

    public function downloadDocument($id)
    {
        // Kiểm tra xác thực người dùng
        if (!Auth::check()) {
            abort(403, 'Vui lòng đăng nhập để tải tài liệu.');
        }

        // Tìm tài liệu
        $document = Document::with(['transaction'])->find($id);

        if (!$document) {
            return back()->with('error', 'Không tìm thấy tài liệu');
        }

        // Kiểm tra quyền truy cập giống như viewDocument
        $user = Auth::user();
        $transaction = $document->transaction;

        $hasAccess = false;
        if ($user->Role == 'Admin') {
            $hasAccess = true;
        } elseif ($user->Role == 'Agent' && $transaction->AgentID == $user->UserID) {
            $hasAccess = true;
        } elseif ($user->Role == 'Owner' && $transaction->OwnerID == $user->UserID) {
            $hasAccess = true;
        } elseif ($user->Role == 'Customer' && $transaction->CusID == $user->UserID) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền tải tài liệu này.');
        }


        // Đường dẫn file trong storage private
        $filePath = storage_path('app/' . $document->FilePath);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Tài liệu không tồn tại trên hệ thống');
        }

        // Tạo tên file download
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = $document->DocumentType . '.' . $extension;

        // Trả về response download
        return response()->download($filePath, $downloadName);
    }



    public function addDocument(Request $request, $transactionId)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // Max 10MB
            'DocumentType' => 'required|string',
        ]);

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return back()->with('error', 'Không tìm thấy giao dịch');
        }

        try {

            $transactionFolder = 'documents/trans_' . $transactionId;
            // Xử lý upload file vào storage private
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Lưu vào thư mục private
            $filePath = $file->storeAs($transactionFolder, $fileName);

            // Tạo bản ghi tài liệu mới
            $document = new Document();

            $document->TransactionID = $transactionId;
            $document->DocumentType = $request->DocumentType;
            $document->FilePath = $filePath; // Chỉ lưu tên file, không lưu đường dẫn đầy đủ
            $document->UploadedDate = now();
            $document->save();

            Log::info('Document added successfully for transaction: ' . $transactionId);

            return back()->with('success', 'Tải lên tài liệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi tải tài liệu: ' . $e->getMessage());
        }
    }

    public function deleteDocument($id)
    {
        // Kiểm tra xác thực người dùng
        if (!Auth::check()) {
            abort(403, 'Vui lòng đăng nhập để thực hiện thao tác này.');
        }

        // Tìm tài liệu
        $document = Document::with(['transaction'])->find($id);

        if (!$document) {
            return back()->with('error', 'Không tìm thấy tài liệu');
        }

        // Kiểm tra quyền xóa - chỉ Admin và Agent liên quan mới được xóa
        $user = Auth::user();
        $transaction = $document->transaction;

        $hasAccess = false;
        if ($user->Role == 'Admin') {
            $hasAccess = true;
        } elseif ($user->Role == 'Agent' && $transaction->AgentID == $user->UserID) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền xóa tài liệu này.');
        }

        try {
            // Đường dẫn file trong storage
            $filePath = storage_path('app/' . $document->FilePath);

            // Xóa file từ storage nếu tồn tại
            if (file_exists($filePath)) {
                unlink($filePath);
            }


            // Xóa bản ghi từ cơ sở dữ liệu
            $document->delete();

            return back()->with('success', 'Xóa tài liệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa tài liệu: ' . $e->getMessage());
        }
    }



}
