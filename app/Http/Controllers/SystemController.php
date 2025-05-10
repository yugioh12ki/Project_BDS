<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\feedback;
use App\Models\Commission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $users = User::all(); // Sửa $user thành $users
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
            $users = User::all();
        }
        else
        {
            $users = User::where('role', $role)->get();
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
        dd($request->all());
        $validated=$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,Email',
            'birth' => 'required|date',
            'sex' => 'required|string',
            'identity_card' => 'required|string|max:12|regex:/^(\d{9}|\d{12})$/',
            'phone' => 'required|string|max:10|regex:/^(\d{10})$/',
            'address' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'role' => 'required|string',
            'password' => 'required|string|min:6|max:30',
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

        return redirect()->route('admin.users.create')->with('success', 'Người dùng đã được tạo thành công.');
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

    public function getProperty()
    {
        $columns = Schema::getColumnListing('properties');
        $properties = Property::all();
        if ($columns === null || $properties->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.property', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.property', compact('columns','properties')); // Đảm bảo biến truyền vào view là $users
    }

    public function getAppointment()
    {
        $columns = Schema::getColumnListing('appointment');
        $appointment = Appointment::all();
        if ($columns === null || $appointment->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.appointment', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.appointment', compact('columns','appointment')); // Đảm bảo biến truyền vào view là $users
    }

    public function getTransaction()
    {
        $columns = Schema::getColumnListing('transactions');
        $transaction = Transaction::all();
        if ($columns === null || $transaction->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.transaction', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.transaction', compact('columns','transaction')); // Đảm bảo biến truyền vào view là $users
    }

    public function getFeedback()
    {
        $columns = Schema::getColumnListing('feedback');
        $feedback = feedback::all();
        if ($columns === null || $feedback->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.feedback', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.feedback', compact('columns','feedback')); // Đảm bảo biến truyền vào view là $users
    }

    public function getCommission()
    {
        $columns = Schema::getColumnListing('commission');
        $commission = Commission::all();
        if ($columns === null || $commission->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.commission', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.commission', compact('columns','commission')); // Đảm bảo biến truyền vào view là $users
    }



}
