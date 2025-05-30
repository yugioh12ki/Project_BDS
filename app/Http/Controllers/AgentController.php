<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Appointment;
use App\Models\Commission;

class AgentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Lấy các bất động sản được giao cho agent này
        $properties = Property::with(['danhMuc', 'chiTiet', 'images', 'chusohuu'])
            ->where('AgentID', $user->UserID)
            ->orderBy('PostedDate', 'desc')
            ->paginate(10);

        // Lấy các cuộc hẹn của agent
        $appointments = Appointment::with(['property', 'user_customer', 'user_owner'])
            ->where('AgentID', $user->UserID)
            ->where('Status', 'Pending')
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(5)
            ->get();

        // Lấy hoa hồng
        $commissions = Commission::with(['trans_agent', 'commission_property'])
            ->where('AgentID', $user->UserID)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Thống kê
        $stats = [
            'total_properties' => Property::where('AgentID', $user->UserID)->count(),
            'active_properties' => Property::where('AgentID', $user->UserID)->where('Status', 'active')->count(),
            'pending_appointments' => Appointment::where('AgentID', $user->UserID)->where('Status', 'Pending')->count(),
            'total_commission' => Commission::where('AgentID', $user->UserID)->where('StatusCommission', 'Success')->sum('Amount'),
        ];

        return view('agent.dashboard', compact('user', 'properties', 'appointments', 'commissions', 'stats'));
    }
}
