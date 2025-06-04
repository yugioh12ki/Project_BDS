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
    }    public function index()
    {
        $agent = Auth::user();
        
        // Get transactions related to agent's properties
        $transactions = Transaction::with(['property', 'trans_cus', 'trans_owner'])
            ->whereHas('property', function($query) use ($agent) {
                $query->where('AgentID', $agent->UserID);
            })
            ->orderBy('TransactionDate', 'desc')
            ->paginate(10);

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

    /**
     * Display property details including images and map
     * @param string $id PropertyID
     * @return \Illuminate\View\View
     */
    public function propertyDetails($id)
    {
        $agent = Auth::user();
        
        // Get property details with eager loading for images and details
        $property = Property::with([
            'danhMuc', 
            'owner', 
            'chiTiet',
            'images'
        ])
        ->where('PropertyID', $id)
        ->where('AgentID', $agent->UserID) // Ensure agent can only see their assigned properties
        ->firstOrFail();
        
        // Format address for Google Maps
        $mapAddress = urlencode("{$property->Address}, {$property->Ward}, {$property->District}, {$property->Province}");
        
        return view('agents.property-detail-modal', compact('property', 'agent', 'mapAddress'));
    }

    /**
     * Get property details for AJAX modal
     * @param string $id PropertyID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPropertyDetails($id)
    {
        try {
            // Get property details with all necessary relationships
            $property = Property::with([
                'danhMuc', 
                'owner', 
                'chiTiet',
                'images'
            ])
            ->where('PropertyID', $id)
            ->where('Status', 'active')
            ->first();
            
            if (!$property) {
                return response()->json(['error' => 'Không tìm thấy bất động sản'], 404);
            }
            
            // Get property image
            $imageUrl = null;
            $thumbnailImage = $property->images->where('IsThumbnail', 1)->first();
            $firstImage = $property->images->first();
            
            if ($thumbnailImage) {
                $imageUrl = $thumbnailImage->ImageURL ?: ($thumbnailImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($thumbnailImage->ImagePath) : null);
            } elseif ($firstImage) {
                $imageUrl = $firstImage->ImageURL ?: ($firstImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($firstImage->ImagePath) : null);
            }            // Format property data for modal
            $propertyData = [
                'PropertyID' => $property->PropertyID,
                'Title' => $property->Title,
                'Address' => $property->Address,
                'Ward' => $property->Ward,
                'District' => $property->District,
                'Province' => $property->Province,
                'Price' => $property->Price,
                'TypePro' => $property->TypePro,
                'Status' => $property->Status,
                'Description' => $property->Description,
                'PostedDate' => $property->PostedDate,
                'Latitude' => $property->Latitude ?? null,
                'Longitude' => $property->Longitude ?? null,
                
                // Owner information
                'owner' => $property->owner ? [
                    'Name' => $property->owner->Name,
                    'Phone' => $property->owner->Phone,
                    'Email' => $property->owner->Email,
                    'Address' => $property->owner->Address
                ] : null,
                  // Category information
                'danhMuc' => $property->danhMuc ? [
                    'ten_pro' => $property->danhMuc->ten_pro,
                    'Type' => $property->danhMuc->Type
                ] : null,
                  // Detail property information
                'chiTiet' => $property->chiTiet ? [
                    'Floor' => $property->chiTiet->Floor,
                    'Area' => $property->chiTiet->HouseLength && $property->chiTiet->HouseWidth 
                        ? $property->chiTiet->HouseLength * $property->chiTiet->HouseWidth 
                        : null,
                    'HouseLength' => $property->chiTiet->HouseLength,
                    'HouseWidth' => $property->chiTiet->HouseWidth,
                    'Bedroom' => $property->chiTiet->Bedroom,
                    'Bath_WC' => $property->chiTiet->Bath_WC,
                    'Balcony' => $property->chiTiet->Balcony,
                    'Levelhouse' => $property->chiTiet->Levelhouse,
                    'Road' => $property->chiTiet->Road,
                    'legal' => $property->chiTiet->legal,
                    'view' => $property->chiTiet->view,
                    'near' => $property->chiTiet->near,
                    'Interior' => $property->chiTiet->Interior,
                    'WaterPrice' => $property->chiTiet->WaterPrice,
                    'PowerPrice' => $property->chiTiet->PowerPrice,
                    'Utilities' => $property->chiTiet->Utilities
                ] : null,
                
                // Include all property images
                'images' => $property->images->map(function($image) {
                    return [
                        'ImageID' => $image->ImageID,
                        'PropertyID' => $image->PropertyID,
                        'ImagePath' => $image->ImagePath ? asset(str_replace('public/', 'storage/', $image->ImagePath)) : null,
                        'ImageURL' => $image->ImageURL,
                        'Caption' => $image->Caption,
                        'IsThumbnail' => $image->IsThumbnail
                    ];
                }),
                
                // Owner information
                'owner_name' => $property->owner ? $property->owner->Name : null,
                'owner_phone' => $property->owner ? $property->owner->Phone : null,
                'owner_email' => $property->owner ? $property->owner->Email : null,
                  // Detail property information                'floor' => $property->chiTiet ? $property->chiTiet->Floor : null,
                'area' => $property->chiTiet ? ($property->chiTiet->HouseLength && $property->chiTiet->HouseWidth 
                    ? $property->chiTiet->HouseLength * $property->chiTiet->HouseWidth 
                    : null) : null,
                'bedroom' => $property->chiTiet ? $property->chiTiet->Bedroom : null,
                'bathroom' => $property->chiTiet ? $property->chiTiet->Bath_WC : null,
                'balcony' => $property->chiTiet ? $property->chiTiet->Balcony : null,
                'levelhouse' => $property->chiTiet ? $property->chiTiet->Levelhouse : null,
                'property_type' => $property->danhMuc ? $property->danhMuc->Type : null,
                'road' => $property->chiTiet ? $property->chiTiet->Road : null,
                'legal' => $property->chiTiet ? $property->chiTiet->legal : null,
                'interior' => $property->chiTiet ? $property->chiTiet->Interior : null,
                'water_price' => $property->chiTiet ? $property->chiTiet->WaterPrice : null,
                'power_price' => $property->chiTiet ? $property->chiTiet->PowerPrice : null,
                'utilities' => $property->chiTiet ? $property->chiTiet->Utilities : null,
                
                // Map coordinates for Google Maps
                'latitude' => $property->Latitude,
                'longitude' => $property->Longitude,
                'map_address' => urlencode($property->Address . ', ' . $property->Ward . ', ' . $property->District . ', ' . $property->Province)
            ];
            
            return response()->json(['success' => true, 'property' => $propertyData]);
            
        } catch (\Exception $e) {
            Log::error('Error getting property details: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi tải thông tin bất động sản'], 500);
        }
    }    /**
     * Search and filter transactions
     */
    public function search(Request $request)
    {
        $agent = Auth::user();
        $query = Transaction::with(['property', 'trans_cus', 'trans_owner'])
            ->whereHas('property', function($query) use ($agent) {
                $query->where('AgentID', $agent->UserID);
            });

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('TransactionID', 'like', "%{$search}%")
                  ->orWhereHas('property', function($query) use ($search) {
                      $query->where('Title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('trans_cus', function($query) use ($search) {
                      $query->where('Name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('TranStatus', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('TransactionType', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('TransactionDate', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('TransactionDate', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('TransactionDate', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total()
            ]
        ]);
    }    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $agent = Auth::user();
        $query = Transaction::with(['property', 'trans_cus', 'trans_owner'])
            ->whereHas('property', function($query) use ($agent) {
                $query->where('AgentID', $agent->UserID);
            });

        // Apply same filters as search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('TransactionID', 'like', "%{$search}%")
                  ->orWhereHas('property', function($query) use ($search) {
                      $query->where('Title', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('TranStatus', $request->status);
        }

        $transactions = $query->orderBy('TransactionDate', 'desc')->get();

        $filename = 'transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($file, [
                'Mã giao dịch',
                'Tên bất động sản', 
                'Khách hàng',
                'Chủ nhà',
                'Giá trị',
                'Trạng thái',
                'Loại giao dịch',
                'Ngày tạo'
            ]);            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->TransactionID,
                    $transaction->property->Title ?? '',
                    $transaction->trans_cus->Name ?? '',
                    $transaction->trans_owner->Name ?? '',
                    number_format($transaction->TotalPrice ?? 0, 0, ',', '.') . ' VND',
                    $transaction->TranStatus ?? '',
                    $transaction->TransactionType ?? '',
                    $transaction->TransactionDate ? date('d/m/Y H:i', strtotime($transaction->TransactionDate)) : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }    /**
     * Show transaction details
     */
    public function show($id)
    {
        $agent = Auth::user();
        
        $transaction = Transaction::with(['property', 'trans_cus', 'trans_owner'])
            ->whereHas('property', function($query) use ($agent) {
                $query->where('AgentID', $agent->UserID);
            })
            ->where('TransactionID', $id)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Không tìm thấy giao dịch'], 404);
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->TransactionID,
                'property_title' => $transaction->property->Title ?? '',
                'property_address' => $transaction->property->Address ?? '',
                'customer_name' => $transaction->trans_cus->Name ?? '',
                'customer_phone' => $transaction->trans_cus->Phone ?? '',
                'owner_name' => $transaction->trans_owner->Name ?? '',
                'owner_phone' => $transaction->trans_owner->Phone ?? '',
                'amount' => $transaction->TotalPrice,
                'formatted_amount' => number_format($transaction->TotalPrice ?? 0, 0, ',', '.') . ' VND',
                'status' => $transaction->TranStatus,
                'type' => $transaction->TransactionType,
                'description' => $transaction->Description ?? '',
                'created_at' => $transaction->TransactionDate ? date('d/m/Y H:i', strtotime($transaction->TransactionDate)) : '',
                'updated_at' => $transaction->TransactionDate ? date('d/m/Y H:i', strtotime($transaction->TransactionDate)) : ''
            ]
        ]);
    }
}
