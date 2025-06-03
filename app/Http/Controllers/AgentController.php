<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\profile_agent;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Transaction;
use App\Models\DetailProperty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AgentController extends Controller
{    /**
     * Display agent dashboard
     */
    public function dashboard()
    {
        // Get current agent data
        $agent = Auth::user();
        
        // Count of properties managed by this agent
        $propertyCount = Property::where('AgentID', $agent->UserID)->count();        // Get recent appointments
        $recentAppointments = Appointment::where('AgentID', $agent->UserID)
            ->with(['property', 'cusUser', 'ownerUser'])
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(10)
            ->get();
          // Get recent properties assigned to this agent
        $recentProperties = Property::where('AgentID', $agent->UserID)
            ->with('owner')
            ->orderBy('PostedDate', 'desc')
            ->limit(10)
            ->get();
        
        return view('agents.dashboard', compact('agent', 'propertyCount', 'recentAppointments', 'recentProperties'));
    }
      /**
     * Display listings managed by this agent
     */
    public function brokers()
    {
        $agent = Auth::user();
        
        // Lấy danh sách properties được phân công cho agent này với status = 'active'
        $properties = Property::with(['danhMuc', 'owner'])
            ->where('AgentID', $agent->UserID)
            ->where('Status', 'active')
            ->orderBy('PostedDate', 'desc')
            ->get();
        
        // Nhóm properties theo District để hiển thị theo quận/huyện
        $propertiesByDistrict = $properties->groupBy('District');

        return view('agents.brokers', compact('properties', 'propertiesByDistrict', 'agent'));
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

        $owners = User::where('Role', 'Owner')->get();
        $customers = User::where('Role', 'Customer')->get();
        // Lấy danh sách properties với eager loading owner - đảm bảo load relationship
        $properties = Property::with(['owner' => function($query) {
                $query->select('UserID', 'Name', 'Phone', 'Email');
            }])
            ->where('AgentID', $agent->UserID)
            ->where('Status', 'active')
            ->select('PropertyID', 'Title', 'OwnerID', 'Address', 'Ward', 'District')
            ->get();
            
        // Tạo mảng JavaScript friendly cho properties
        $propertyList = [];
        foreach ($properties as $property) {
            $propertyList[] = [
                'id' => $property->PropertyID,
                'title' => $property->Title,
                'ownerId' => $property->OwnerID,
                'ownerName' => $property->owner ? $property->owner->Name : 'Không xác định',
                'address' => $property->Address ?? '',
                'district' => $property->District ?? '',
                'ward' => $property->Ward ?? '',
            ];
        }

        // Truyền cả 2 biến vào view
        return view('agents.appointments', compact('appointments','owners', 'customers', 'properties', 'propertyList'));
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
    
    /**
     * Create a new appointment (AJAX Compatible)
     */
    public function createAppointment(Request $request)
    {
        // Validate request data
        $request->validate([
            'PropertyID' => 'required|exists:properties,PropertyID',
            'CusID' => 'required|exists:user,UserID',
            'OwnerID' => 'required|exists:user,UserID',
            'TitleAppoint' => 'required|string|max:255',
            'DescAppoint' => 'required|string',
            'AppointmentDateStart' => 'required|date',
            'AppointmentDateEnd' => 'required|date|after:AppointmentDateStart',
        ]);

        // Get authenticated agent
        $agent = Auth::user();
        if (!$agent) {
            return back()->with('error', 'Bạn cần đăng nhập để tạo lịch hẹn');
        }

        // Get property with owner information
        $property = Property::with('owner')->findOrFail($request->PropertyID);

        // Check if agent is assigned to this property (optional check)
        if ($property->AgentID !== null && $property->AgentID !== $agent->UserID) {
            return back()->with('error', 'Bạn không được phân công cho bất động sản này');
        }

        // Auto-correct OwnerID to match property's owner
        $correctOwnerID = $property->OwnerID;

        try {
            // Create appointment - ONLY ONE METHOD
            $appointment = Appointment::create([
                'PropertyID' => $property->PropertyID,
                'AgentID' => $agent->UserID,
                'CusID' => $request->CusID,
                'OwnerID' => $correctOwnerID,
                'TitleAppoint' => $request->TitleAppoint,
                'DescAppoint' => $request->DescAppoint,
                'AppointmentDateStart' => $request->AppointmentDateStart,
                'AppointmentDateEnd' => $request->AppointmentDateEnd,
                'Status' => 'Khởi tạo'
            ]);

            Log::info('Appointment created successfully', [
                'AppointmentID' => $appointment->AppointmentID,
                'PropertyID' => $property->PropertyID,
                'AgentID' => $agent->UserID,
                'CusID' => $request->CusID
            ]);

            return back()->with('success', 'Tạo lịch hẹn thành công');

        } catch (\Exception $e) {
            Log::error('Failed to create appointment', [
                'error' => $e->getMessage(),
                'PropertyID' => $request->PropertyID,
                'AgentID' => $agent->UserID
            ]);

            return back()->with('error', 'Lỗi khi tạo lịch hẹn: ' . $e->getMessage());
        }
    }

    public function searchProperties(Request $request)
    {
        $ownerId = $request->query('owner_id');
        if (!$ownerId) return response()->json([]);

        $properties = Property::where('OwnerID', $ownerId)
            ->whereIn('Status', ['active', 'Active', 'approved', 'Approved'])
            ->get(['PropertyID as id', 'Title as title']);

        return response()->json($properties);
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

    public function transactions()
    {
        $transactions = Transaction::all();

        return view('agents.transactions', compact('transactions'));
    }

    /**
     * Update appointment status
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Thành công,Đã hủy,Hoàn Thành,Chờ xử lý'
        ]);

        $appointment = Appointment::with(['cusUser', 'ownerUser', 'property'])->findOrFail($id);
        
        // Kiểm tra xem agent có quyền cập nhật trạng thái lịch hẹn này không
        if ($appointment->AgentID !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật lịch hẹn này');
        }

        $oldStatus = $appointment->Status;
        $requestStatus = $request->status;
        
        // Map agent status to database status
        $statusMap = [
            'Thành công' => Appointment::STATUS_ACTIVE,    // 'Đang Thực Hiện'
            'Đã hủy' => Appointment::STATUS_CANCELLED,     // 'Hủy Hẹn'
            'Hoàn Thành' => Appointment::STATUS_COMPLETED, // 'Hoàn Thành'
            'Chờ xử lý' => Appointment::STATUS_PENDING     // 'Khởi Tạo'
        ];
        
        $newStatus = $statusMap[$requestStatus] ?? $requestStatus;
        
        // Only update if status actually changed
        if ($oldStatus !== $newStatus) {
            $appointment->Status = $newStatus;
            $appointment->save();            // Send notifications to customer and owner
            try {
                $agentName = Auth::user()->Name ?? 'Người môi giới';
                
                if ($appointment->cusUser) {
                    $appointment->cusUser->notify(new \App\Notifications\AppointmentStatusChanged($appointment, $oldStatus, $newStatus, $agentName));
                }
                
                if ($appointment->ownerUser) {
                    $appointment->ownerUser->notify(new \App\Notifications\AppointmentStatusChanged($appointment, $oldStatus, $newStatus, $agentName));
                }
                
                Log::info('Appointment status updated and notifications sent', [
                    'appointment_id' => $id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'customer_id' => $appointment->CusID,
                    'owner_id' => $appointment->OwnerID
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send appointment notification', [
                    'appointment_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->back()->with('success', 'Cập nhật trạng thái lịch hẹn thành công');
        }

        return redirect()->back()->with('info', 'Trạng thái lịch hẹn không thay đổi');
    }
    
    public function getNotifications(Request $request)
    {
        $agentId = Auth::user()->UserID;
        $notifications = [];

        $appointments = Appointment::with(['property', 'ownerUser', 'cusUser'])
            ->where('AgentID', $agentId)
            ->whereIn('Status', ['Đang Thực Hiện', 'Hủy Hẹn'])
            ->whereDate('AppointmentDateStart', '>=', Carbon::now()->subDays(30))
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(15)
            ->get();

        foreach ($appointments as $appointment) {
            $appointmentTime = Carbon::parse($appointment->AppointmentDateStart);

            if ($appointment->Status === 'Đang Thực Hiện') {
                $message = "Chủ sở hữu {$appointment->ownerUser->Name} đã XÁC NHẬN lịch hẹn cho BĐS: {$appointment->property->Title}";
                $title = 'Lịch hẹn được xác nhận';
            } else { 
                $message = "Chủ sở hữu {$appointment->ownerUser->Name} đã HỦY lịch hẹn cho BĐS: {$appointment->property->Title}";
                $title = 'Lịch hẹn bị hủy';
            }

            $notifications[] = [
                'id' => 'appointment_' . $appointment->AppointmentID,
                'type' => 'appointment',
                'title' => $title,
                'message' => $message,
                'time' => $appointmentTime->diffForHumans(),
                'url' => route('agent.appointments'),
                'is_read' => false,
                'data' => [
                    'appointment_id' => $appointment->AppointmentID,
                    'property_title' => $appointment->property->Title,
                    'owner_name' => $appointment->ownerUser->Name,
                    'customer_name' => $appointment->cusUser->Name ?? 'Khách hàng',
                    'status' => $appointment->Status,
                    'raw_time' => $appointmentTime->timestamp,
                ]
            ];
        }

        usort($notifications, function($a, $b) {
            $timeA = $a['data']['raw_time'] ?? 0;
            $timeB = $b['data']['raw_time'] ?? 0;
            return $timeB - $timeA;
        });

        $notifications = array_slice($notifications, 0, 15);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => count($notifications)
            ]);
        }

        return $notifications;
    }

    public function searchCustomers(Request $request)
    {
        $searchTerm = $request->query('term');
        $customers = User::where('Role', 'Customer')
            ->where(function ($q) use ($searchTerm) {
                $q->where('Name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('Phone', 'LIKE', '%' . $searchTerm . '%');
            })
            ->limit(10)
            ->get(['UserID as id', 'Name as name', 'Phone as phone']);
        return response()->json($customers);
    }
    
    /**
     * Tìm kiếm chủ sở hữu bất động sản theo tên hoặc email
     */
    public function searchOwners(Request $request)
    {
        $searchTerm = $request->query('term');
        if (!$searchTerm || strlen($searchTerm) < 2) {
            return response()->json(['owners' => []]);
        }
        $owners = User::where('Role', 'Owner')
            ->where('Name', 'like', '%' . $searchTerm . '%')
            ->limit(10)
            ->get(['UserID as id', 'Name as name', 'Phone as phone']);
        return response()->json(['owners' => $owners]);
    }
    
    

    public function getAppointmentsByStatus(Request $request, $status)
    {
        $agentId = Auth::user()->UserID;

        // Map filter status to database status
        $statusMap = [
            'khoitao' => 'Khởi Tạo',
            'dangthuchien' => 'Đang Thực Hiện', 
            'hoanthanh' => 'Hoàn Thành',
            'huyhen' => 'Hủy Hẹn'
        ];

        if (!isset($statusMap[$status])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $dbStatus = $statusMap[$status];

        $appointments = Appointment::with(['property.images', 'ownerUser', 'cusUser'])
            ->where('AgentID', $agentId)
            ->where('Status', $dbStatus)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        $formattedAppointments = $appointments->map(function ($appointment) {
            $propertyImage = null;
            if ($appointment->property && $appointment->property->images->first()) {
                $propertyImage = 'data:image/jpeg;base64,' . base64_encode($appointment->property->images->first()->ImagePath);
            }            return [
                'id' => $appointment->AppointmentID,
                'property_title' => $appointment->property ? $appointment->property->Title : $appointment->TitleAppoint,
                'property_address' => $appointment->property ? $appointment->property->Address : 'N/A',
                'property_image' => $propertyImage,
                'owner_name' => $appointment->ownerUser ? $appointment->ownerUser->Name : 'N/A',
                'owner_phone' => $appointment->ownerUser ? $appointment->ownerUser->Phone : '',
                'owner_initials' => $appointment->ownerUser ? strtoupper(substr($appointment->ownerUser->Name, 0, 2)) : 'N/A',
                'customer_name' => $appointment->cusUser ? $appointment->cusUser->Name : 'N/A',
                'customer_phone' => $appointment->cusUser ? $appointment->cusUser->Phone : '',
                'customer_initials' => $appointment->cusUser ? strtoupper(substr($appointment->cusUser->Name, 0, 2)) : 'N/A',
                'title' => $appointment->TitleAppoint,
                'description' => $appointment->DescAppoint,
                'date' => $appointment->AppointmentDateStart ? Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y') : 'N/A',
                'end_date' => $appointment->AppointmentDateEnd ? Carbon::parse($appointment->AppointmentDateEnd)->format('d/m/Y') : 'N/A',
                'start_time' => $appointment->AppointmentDateStart ? Carbon::parse($appointment->AppointmentDateStart)->format('H:i') : 'N/A',
                'end_time' => $appointment->AppointmentDateEnd ? Carbon::parse($appointment->AppointmentDateEnd)->format('H:i') : 'N/A',
                'status' => $appointment->Status,
                'status_badge' => $this->getStatusBadge($appointment->Status),
                'can_update' => true, // Agent can always update
            ];
        });

        return response()->json([
            'success' => true,
            'appointments' => $formattedAppointments,
            'count' => $appointments->count(),
            'status' => $status
        ]);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'Khởi Tạo':
                return '<span class="badge bg-warning text-dark">Khởi Tạo</span>';
            case 'Đang Thực Hiện':
                return '<span class="badge bg-success">Đang Thực Hiện</span>';
            case 'Hoàn Thành':
                return '<span class="badge bg-info">Hoàn Thành</span>';
            case 'Hủy Hẹn':
                return '<span class="badge bg-danger">Hủy Hẹn</span>';
            default:
                return '<span class="badge bg-secondary">' . $status . '</span>';
        }
    }

    public function finishAppointment($id)
    {
        $agent = Auth::user();
        $appointment = Appointment::with('ownerUser', 'cusUser')->findOrFail($id);

        if (
            $appointment->AgentID != $agent->UserID
            || $appointment->Status !== 'Đang Thực Hiện'
            || Carbon::now()->lt(Carbon::parse($appointment->AppointmentDateEnd))
        ) {
            return redirect()->back()->with('error', 'Bạn không thể hoàn thành lịch hẹn này.');
        }

        $oldStatus = $appointment->Status;
        $appointment->Status = 'Hoàn Thành';
        $appointment->save();

        if ($appointment->ownerUser) {
            $appointment->ownerUser->notify(
                new \App\Notifications\AppointmentStatusChanged(
                    $appointment,
                    $oldStatus,
                    'Hoàn Thành',
                    $agent->Name 
                )
            );
        }

        if ($appointment->cusUser) {
            $appointment->cusUser->notify(
                new \App\Notifications\AppointmentStatusChanged(
                    $appointment,
                    $oldStatus,
                    'Hoàn Thành',
                    $agent->Name
                )
            );
        }

        return redirect()->back()->with('success', 'Lịch hẹn đã hoàn thành!');
    }
}
