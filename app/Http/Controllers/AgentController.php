<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\profile_agent;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Display agent dashboard
     */
    public function dashboard()
    {
        // Get current agent data
        $agent = Auth::user();
        
        // Count of properties managed by this agent
        $propertyCount = 0; // Replace with actual count from database
        
        // Recent listings or appointments
        $recentItems = [];
        
        return view('agents.dashboard', compact('agent', 'propertyCount', 'recentItems'));
    }
    
    /**
     * Display listings managed by this agent
     */
    public function brokers()
    {
        $agent = Auth::user();
    
        $properties = Property::where('AgentID', $agent->UserID)
            ->where('Status', 'active')
            ->orderBy('PostedDate', 'desc')
            ->get();

        return view('agents.brokers', compact('properties'));
    }
    
    /**
     * Display appointments for this agent
     */
    public function appointments()
    {
        $agent = Auth::user();
        
        // Lấy danh sách cuộc hẹn hiện tại
        $appointments = Appointment::with(['cusUser', 'property', 'ownerUser'])
            ->where('AgentID', $agent->UserID)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        // Chỉ lấy những BĐS đang active và được phân công cho agent
        $properties = Property::with(['owner'])  // Thêm relationship với owner
            ->select('PropertyID', 'Title', 'Address', 'Ward', 'District', 'OwnerID')
            ->where('AgentID', $agent->UserID)
            ->where('Status', 'active')
            ->orderBy('PostedDate', 'desc')
            ->get();

        return view('agents.appointments', compact('appointments', 'properties')); 
    }
    
    /**
     * Display agent profile
     */
    public function profile()
    {
        $agent = Auth::user();
        return view('agents.profile', compact('agent'));
    }
    
    /**
     * Update agent profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        
        // Update agent profile if exists
        if ($profile = profile_agent::where('user_id', $user->id)->first()) {
            $profile->phone = $request->phone;
            $profile->save();
        }
        
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    // Thêm method tạo lịch hẹn mới
    public function createAppointment(Request $request)
    {
        $request->validate([
            'PropertyID' => 'required|exists:properties,PropertyID',
            'CusID' => 'required|exists:user,UserID',
            'TitleAppoint' => 'required|string|max:255',
            'DescAppoint' => 'required|string',
            'AppointmentDateStart' => 'required|date',
            'AppointmentDateEnd' => 'required|date|after:AppointmentDateStart',
        ]);

        $agent = Auth::user();
        $property = Property::findOrFail($request->PropertyID);

        // Kiểm tra xem agent có được phân công cho BĐS này không
        if ($property->AgentID !== $agent->UserID) {
            return back()->with('error', 'Bạn không được phân công cho bất động sản này');
        }

        $appointment = new Appointment();
        $appointment->PropertyID = $request->PropertyID;
        $appointment->AgentID = $agent->UserID;
        $appointment->CusID = $request->CusID;
        $appointment->OwnerID = $property->OwnerID;
        $appointment->TitleAppoint = $request->TitleAppoint;
        $appointment->DescAppoint = $request->DescAppoint;
        $appointment->AppointmentDateStart = $request->AppointmentDateStart;
        $appointment->AppointmentDateEnd = $request->AppointmentDateEnd;
        $appointment->Status = 'Chờ xử lý';

        $appointment->save();

        return back()->with('success', 'Tạo lịch hẹn thành công');
    }
}
