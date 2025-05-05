<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use Illuminate\Support\Facades\Schema;

class SystemController extends Controller
{
    //
    public function admin()
    {
        return view( '_system.index');
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

    public function getProperty()
    {
        $columns = Schema::getColumnListing('properties');
        $property = Property::all();
        if ($columns === null || $property->isEmpty()) {
            $error = '404 Error: Lỗi lấy dữ liệu'; // Thông báo lỗi
            return view('_system.property', compact('error')); // Truyền thông báo lỗi sang view
        }
        return view('_system.property', compact('columns','property')); // Đảm bảo biến truyền vào view là $users
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
}
