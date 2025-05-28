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

    // Thêm method tạo lịch hẹn mới
    public function createAppointment(Request $request)
    {
        $request->validate([
            'PropertyID' => 'required|exists:properties,PropertyID',
            'CusID' => 'required|exists:user,UserID',
            'OwnerID' => 'required|exists:user,UserID',
            'TitleAppoint' => 'required|string|max:255',
            'DescAppoint' => 'required|string',
            'AppointmentDateStart' => 'required|date',
            'AppointmentDateEnd' => 'required|date|after:AppointmentDateStart',
        ]);

        $agent = Auth::user();
        
        // Lấy thông tin bất động sản từ ID
        $property = Property::with('owner')->findOrFail($request->PropertyID);

        // Kiểm tra xem agent có được phân công cho BĐS này không
        if ($property->AgentID !== $agent->UserID) {
            return back()->with('error', 'Bạn không được phân công cho bất động sản này');
        }
        
        // Kiểm tra xem OwnerID trong request có khớp với OwnerID của bất động sản không
        if ($property->OwnerID != $request->OwnerID) {
            \Log::warning('Owner ID mismatch', [
                'PropertyOwnerID' => $property->OwnerID,
                'RequestOwnerID' => $request->OwnerID
            ]);
            return back()->with('error', 'Chủ sở hữu không khớp với bất động sản đã chọn');
        }

        // Tạo cuộc hẹn mới với đảm bảo thông tin chính xác từ cơ sở dữ liệu
        $appointment = new Appointment();
        $appointment->PropertyID = $property->PropertyID; // Lấy từ bất động sản đã tìm thấy
        $appointment->AgentID = $agent->UserID;
        $appointment->CusID = $request->CusID;
        $appointment->OwnerID = $property->OwnerID; // Lấy từ bất động sản đã tìm thấy
        $appointment->TitleAppoint = $request->TitleAppoint;
        $appointment->DescAppoint = $request->DescAppoint;
        $appointment->AppointmentDateStart = $request->AppointmentDateStart;
        $appointment->AppointmentDateEnd = $request->AppointmentDateEnd;
        $appointment->Status = 'Chờ xử lý';

        $appointment->save();
        
        // Log thông tin cuộc hẹn để debug
        \Log::info('Appointment created successfully', [
            'AppointmentID' => $appointment->AppointmentID,
            'PropertyID' => $property->PropertyID,
            'PropertyTitle' => $property->Title,
            'OwnerID' => $property->OwnerID,
            'OwnerName' => $property->owner->Name ?? 'Không xác định',
            'AgentID' => $agent->UserID,
            'CusID' => $request->CusID
        ]);

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

        $appointment = Appointment::findOrFail($id);
        
        // Kiểm tra xem agent có quyền cập nhật trạng thái lịch hẹn này không
        if ($appointment->AgentID !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật lịch hẹn này');
        }

        $appointment->Status = $request->status;
        $appointment->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái lịch hẹn thành công');
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
        $agent = Auth::user();
        $ownerId = $request->query('ownerId');
        
        // Log đầu vào để debug
        \Log::info('getOwnerProperties called', [
            'agent_id' => $agent ? $agent->UserID : null,
            'owner_id' => $ownerId,
            'request_params' => $request->all()
        ]);
        
        if (!$ownerId) {
            \Log::warning('Missing ownerId parameter');
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
        \Log::debug('Finding properties for owner', ['ownerId' => $ownerId]);
        
        // Truy vấn trực tiếp bảng properties
        $properties = DB::table('properties')
                        ->where('OwnerID', $ownerId)
                        ->get();
        
        // Ghi log số lượng bất động sản tìm thấy
        \Log::debug('Raw Properties Found', [
            'ownerID' => $ownerId,
            'properties_count' => $properties->count(),
            'first_property' => $properties->first(),
            'all_property_ids' => $properties->pluck('PropertyID')->toArray()
        ]);
        
        // Định dạng dữ liệu trả về cho frontend
        $formattedProperties = $properties->map(function($property) {
            // Lấy thông tin danh mục nếu có
            $category = null;
            if (!empty($property->PropertyType)) {
                $category = DB::table('protype_bds')->where('Protype_ID', $property->PropertyType)->first();
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
    }

    /**
     * Tìm kiếm khách hàng theo tên hoặc email để tạo appointment
     */
    public function searchCustomersForAppointment(Request $request)
    {
        $searchTerm = $request->query('term');
        
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
}
