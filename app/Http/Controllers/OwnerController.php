<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Appointment;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Lấy các bất động sản của chủ sở hữu này
        $properties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $user->UserID)
            ->orderBy('PostedDate', 'desc')
            ->paginate(10);

        // Lấy các cuộc hẹn liên quan đến bất động sản của chủ sở hữu
        $appointments = Appointment::with(['property', 'user_customer', 'user_agent'])
            ->where('OwnerID', $user->UserID)
            ->where('Status', 'Pending')
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(5)
            ->get();

        // Thống kê
        $stats = [
            'total_properties' => Property::where('OwnerID', $user->UserID)->count(),
            'active_properties' => Property::where('OwnerID', $user->UserID)->where('Status', 'active')->count(),
            'pending_properties' => Property::where('OwnerID', $user->UserID)->where('Status', 'pending')->count(),
            'pending_appointments' => Appointment::where('OwnerID', $user->UserID)->where('Status', 'Pending')->count(),
        ];

        return view('owner.dashboard', compact('user', 'properties', 'appointments', 'stats'));
    }
}
