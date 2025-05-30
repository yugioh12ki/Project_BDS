<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\profile_agent;
use App\Models\Appointment;
use App\Models\User;
use App\Models\DetailProperty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            ->limit(5)
            ->get();
          // Get recent properties assigned to this agent
        $recentProperties = Property::where('AgentID', $agent->UserID)
            ->with('owner')
            ->orderBy('PostedDate', 'desc')
            ->limit(5)
            ->get();
        
        return view('agents.dashboard', compact('agent', 'propertyCount', 'recentAppointments', 'recentProperties'));
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
        return view('agents.appointments', compact('appointments', 'properties', 'propertyList'));
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

    public function transactions()
    {
        // Implement transactions view
    }

    /**
     * Update appointment status
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAppointmentStatus(Request $request, $id)
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
        $newStatus = $request->status;
        
        // Only update if status actually changed
        if ($oldStatus !== $newStatus) {
            $appointment->Status = $newStatus;
            $appointment->save();

            // Send notifications to customer and owner
            try {
                if ($appointment->cusUser) {
                    $appointment->cusUser->notify(new \App\Notifications\AppointmentStatusChanged($appointment, $oldStatus, $newStatus));
                }
                
                if ($appointment->ownerUser) {
                    $appointment->ownerUser->notify(new \App\Notifications\AppointmentStatusChanged($appointment, $oldStatus, $newStatus));
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
    
    /**
     * Tìm kiếm chủ sở hữu bất động sản theo tên hoặc email
     */
    public function searchOwners(Request $request)
    {
        $searchTerm = $request->query('term');
        $getTop = $request->query('top', false);
        $limit = $request->query('limit', 5);
        
        // Xử lý khi yêu cầu top owners
        if ($getTop) {
            $owners = User::where('Role', 'Owner')
                ->orderBy('Name', 'asc')
                ->take($limit)
                ->get(['UserID as id', 'Name as name', 'Email as email', 'Phone as phone']);
            return response()->json(['owners' => $owners]);
        }
        
        if (!$searchTerm || strlen($searchTerm) < 2) {
            return response()->json(['owners' => []]);
        }
        
        // Tìm kiếm chủ sở hữu theo tên hoặc email
        $owners = User::where('Role', 'Owner')
            ->where(function ($query) use ($searchTerm) {
                $query->where('Name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('Email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('Phone', 'like', '%' . $searchTerm . '%');
            })
            ->take(10)
            ->get(['UserID as id', 'Name as name', 'Email as email', 'Phone as phone']);
        
        return response()->json(['owners' => $owners]);
    }
    
    /**
     * Lấy danh sách bất động sản của một chủ sở hữu
     */
    public function getOwnerProperties(Request $request)
    {
        try {
            $agent = Auth::user();
            $ownerId = $request->query('ownerId');
            
            // Log đầu vào để debug
            Log::info('getOwnerProperties called', [
                'agent_id' => $agent ? $agent->UserID : null,
                'owner_id' => $ownerId,
                'request_params' => $request->all()
            ]);
            
            if (!$ownerId) {
                Log::warning('Missing ownerId parameter');
                return response()->json(['error' => 'OwnerID is required'], 400);
            }
            
            // Tìm chủ sở hữu
            $owner = User::where('UserID', $ownerId)
                         ->where('Role', 'Owner')
                         ->first();
            
            if (!$owner) {
                return response()->json(['error' => 'Không tìm thấy chủ sở hữu'], 404);
            }
            
            // Debug để kiểm tra ID chủ sở hữu
            Log::debug('Finding properties for owner', ['ownerId' => $ownerId]);
              // Sử dụng Eloquent Model để truy vấn - fix status filter to include more possible values
            $properties = \App\Models\Property::where('OwnerID', $ownerId)
                            ->whereIn('Status', ['active', 'Active', 'approved', 'Approved', 'pending', 'Pending']) // Check multiple possible status values
                            ->with('danhMuc') // Eager loading quan hệ
                            ->get();
            
            // Ghi log số lượng bất động sản tìm thấy
            Log::debug('Raw Properties Found', [
                'ownerID' => $ownerId,
                'properties_count' => $properties->count(),
                'first_property' => $properties->first(),
                'all_property_ids' => $properties->pluck('PropertyID')->toArray(),                'filter_conditions' => [
                    'OwnerID' => $ownerId,
                    'Status' => ['active', 'Active', 'approved', 'Approved', 'pending', 'Pending']
                ]
            ]);
        
        // Định dạng dữ liệu trả về cho frontend
        $formattedProperties = $properties->map(function($property) {
            // Với eager loading, danh mục đã được nạp sẵn
            $category = $property->danhMuc;
            
            if (!$category && !empty($property->PropertyType)) {
                try {
                    // Nếu chưa có danh mục, thử lấy trực tiếp
                    $category = \App\Models\DanhMucBDS::find($property->PropertyType);
                    if (!$category) {
                        Log::warning("Không tìm thấy danh mục cho PropertyType: {$property->PropertyType}");
                    }
                } catch (\Exception $e) {
                    Log::error("Lỗi khi truy vấn danh mục bất động sản", [
                        'PropertyType' => $property->PropertyType,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            return [
                'id' => $property->PropertyID,
                'title' => $property->Title ?? 'Không có tiêu đề',
                'address' => $property->Address,
                'district' => $property->District,
                'ward' => $property->Ward,
                'type' => $property->TypePro,
                'price' => $property->Price,
                'formattedPrice' => isset($property->Price) 
                    ? (($property->TypePro == 'Rent') 
                        ? number_format($property->Price) . ' VNĐ/tháng'
                        : number_format($property->Price) . ' VNĐ')
                    : '',
                'fullAddress' => implode(', ', array_filter([$property->Address, $property->Ward, $property->District])),
                'ownerId' => $property->OwnerID,
                'categoryId' => $property->PropertyType,
                'categoryName' => $category ? $category->ten_pro : null,
                'status' => $property->Status
            ];
        });
        
        return response()->json([
            'owner' => [
                'id' => $owner->UserID,
                'name' => $owner->Name,
                'phone' => $owner->Phone,
                'email' => $owner->Email
            ],
            'properties' => $formattedProperties
        ]);
        
        } catch (\Exception $e) {
            // Log lỗi và trả về thông báo lỗi
            Log::error('Error in getOwnerProperties', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Không thể tải thông tin bất động sản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm khách hàng theo tên hoặc email để tạo appointment
     */
    public function searchCustomersForAppointment(Request $request)
    {
        $searchTerm = $request->query('term');
        $getTop = $request->query('top', false);
        $limit = $request->query('limit', 5);
        
        // Xử lý khi yêu cầu top customers
        if ($getTop) {
            $customers = User::where('Role', 'Customer')
                ->orderBy('Name', 'asc')
                ->take($limit)
                ->get(['UserID as id', 'Name as name', 'Email as email', 'Phone as phone']);
            return response()->json(['customers' => $customers]);
        }
        
        if (!$searchTerm || strlen($searchTerm) < 2) {
            return response()->json(['customers' => []]);
        }
        
        // Tìm kiếm khách hàng theo tên hoặc email
        $customers = User::where('Role', 'Customer')
            ->where(function ($query) use ($searchTerm) {
                $query->where('Name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('Email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('Phone', 'like', '%' . $searchTerm . '%');
            })
            ->take(10)
            ->get(['UserID as id', 'Name as name', 'Email as email', 'Phone as phone']);
        
        return response()->json(['customers' => $customers]);
    }

    /**
     * Lấy danh sách bất động sản của một chủ sở hữu thông qua URL REST API
     */
    public function getOwnerPropertiesByUrl($ownerId)
    {
        try {
            $agent = Auth::user();
            
            // Log đầu vào để debug
            Log::info('getOwnerPropertiesByUrl called', [
                'agent_id' => $agent ? $agent->UserID : null,
                'owner_id' => $ownerId,
                'request_method' => request()->method(),
                'request_headers' => request()->headers->all()
            ]);
            
            if (!$ownerId) {
                Log::warning('Missing ownerId parameter');
                return response()->json(['error' => 'OwnerID is required'], 400);
            }
            
            // Tìm chủ sở hữu
            $owner = User::where('UserID', $ownerId)
                         ->where('Role', 'Owner')
                         ->first();
            
            if (!$owner) {
                Log::warning('Owner not found', ['ownerId' => $ownerId]);
                return response()->json(['error' => 'Không tìm thấy chủ sở hữu'], 404);
            }
            
            Log::info('Owner found', [
                'owner_id' => $owner->UserID,
                'owner_name' => $owner->Name
            ]);
            
            // Debug để kiểm tra ID chủ sở hữu
            Log::debug('Finding properties for owner', ['ownerId' => $ownerId]);
            
            // Sử dụng Eloquent Model để truy vấn - fix status filter to include more possible values
            $properties = \App\Models\Property::where('OwnerID', $ownerId)
                            ->whereIn('Status', ['active', 'Active', 'approved', 'Approved', 'pending', 'Pending']) // Check multiple possible status values
                            ->with('danhMuc') // Eager loading quan hệ
                            ->get();
            
            // Ghi log số lượng bất động sản tìm thấy
            Log::debug('Raw Properties Found (REST API)', [
                'ownerID' => $ownerId,
                'properties_count' => $properties->count(),
                'first_property' => $properties->first(),
                'all_property_ids' => $properties->pluck('PropertyID')->toArray()
            ]);
        
            // Định dạng dữ liệu trả về cho frontend
            $formattedProperties = $properties->map(function($property) {
                // Với eager loading, danh mục đã được nạp sẵn
                $category = $property->danhMuc;
                
                if (!$category && !empty($property->PropertyType)) {
                    try {
                        // Nếu chưa có danh mục, thử lấy trực tiếp
                        $category = \App\Models\DanhMucBDS::find($property->PropertyType);
                        if (!$category) {
                            Log::warning("Không tìm thấy danh mục cho PropertyType: {$property->PropertyType}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Lỗi khi truy vấn danh mục bất động sản", [
                            'PropertyType' => $property->PropertyType,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                return [
                    'id' => $property->PropertyID,
                    'title' => $property->Title ?? 'Không có tiêu đề',
                    'address' => $property->Address,
                    'district' => $property->District,
                    'ward' => $property->Ward,
                    'type' => $property->TypePro,
                    'price' => $property->Price,
                    'formattedPrice' => isset($property->Price) 
                        ? (($property->TypePro == 'Rent') 
                            ? number_format($property->Price) . ' VNĐ/tháng'
                            : number_format($property->Price) . ' VNĐ')
                        : '',
                    'fullAddress' => implode(', ', array_filter([$property->Address, $property->Ward, $property->District])),
                    'ownerId' => $property->OwnerID,
                    'categoryId' => $property->PropertyType,
                    'categoryName' => $category ? $category->ten_pro : null,
                    'status' => $property->Status
                ];
            });
            
            return response()->json([
                'owner' => [
                    'id' => $owner->UserID,
                    'name' => $owner->Name,
                    'phone' => $owner->Phone,
                    'email' => $owner->Email
                ],
                'properties' => $formattedProperties
            ]);
            
        } catch (\Exception $e) {
            // Log lỗi và trả về thông báo lỗi
            Log::error('Error in getOwnerPropertiesByUrl', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Không thể tải thông tin bất động sản: ' . $e->getMessage()
            ], 500);
        }
    }
}
