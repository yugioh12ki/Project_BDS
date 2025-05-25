<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\profile_agent;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        
        // Lấy danh sách appointments với eager loading
        $appointments = Appointment::with(['cusUser', 'ownerUser', 'property'])
            ->where('AgentID', $agent->UserID)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        // Lấy danh sách properties với eager loading owner
        $properties = Property::with(['owner'])
            ->where('AgentID', $agent->UserID)
            ->where('Status', 'active')
            ->get();

        // Truyền cả 2 biến vào view
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

    public function searchProperties(Request $request) 
    {
        $agent = Auth::user();
        $searchText = $request->query('term');
        
        // Tìm chính xác property theo title
        $property = Property::with('owner')
            ->where('AgentID', $agent->UserID)
            ->where('Title', 'LIKE', "%{$searchText}%")
            ->first();

        if ($property) {
            return response()->json([
                'id' => $property->PropertyID,
                'title' => $property->Title,
                'ownerName' => $property->owner->Name ?? 'Không xác định'
            ]);
        }

        return response()->json(null);
    }

    public function getRelatedCustomers($propertyId)
    {
        $agent = Auth::user();
        
        return User::whereHas('appoint_customer', function($query) use ($propertyId) {
                $query->where('PropertyID', $propertyId);
            })
            ->select('UserID', 'Name')
            ->get();
    }

    public function searchCustomers(Request $request)
    {
        $term = $request->get('term');
        $propertyId = $request->get('propertyId');

        $customers = Appointment::where('PropertyID', $propertyId)
            ->join('users', 'appointments.CusID', '=', 'users.UserID')
            ->where('users.Name', 'LIKE', "%{$term}%")
            ->select('users.UserID as id', 'users.Name as name', 'users.Phone as phone')
            ->distinct()
            ->get();

        return response()->json([
            'customers' => $customers
        ]);
    }
}
