<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\profile_agent;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Document;
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
            'email' => 'required|email|unique:user,email,' . Auth::id(),
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

    public function index()
    {
        $agent = Auth::user();
        
        // Get transactions related to agent's properties
        $transactions = Transaction::with(['property', 'trans_cus', 'trans_owner'])
            ->whereHas('property', function($query) use ($agent) {
                $query->where('AgentID', $agent->UserID);
            })
            ->orderBy('TransactionDate', 'desc')
            ->get(); // Use get() instead of paginate() for now

        // Get properties managed by this agent for the modal dropdown
        $properties = Property::with(['danhMuc'])
            ->where('AgentID', $agent->UserID)
            ->where('Status', 'active')  // Only active properties
            ->whereNotNull('PropertyID') // Ensure PropertyID is not null
            ->whereNotNull('Title')      // Ensure Title is not null
            ->get()
            ->filter(function($property) {
                return $property !== null && isset($property->PropertyID);
            });

        // Debug: Log properties count and details
        Log::info('Properties count for agent ' . $agent->UserID . ': ' . $properties->count());
        Log::info('Properties details: ', $properties->toArray());
        
        // Get customers (users with role Customer) for the modal dropdown
        $customers = User::where('Role', 'Customer')
            ->select('UserID', 'Name', 'Phone', 'Email')
            ->get();

        Log::info('Customers count: ' . $customers->count());

        // Ensure we always have empty collections, not null
        $transactions = $transactions ?? collect();
        $properties = $properties ?? collect();
        $customers = $customers ?? collect();

        return view('agents.transactions', compact('transactions', 'properties', 'customers'));
    }

    /**
     * Store a new transaction (4-step workflow)
     */
    public function store(Request $request)
    {
        try {
            // Get authenticated agent first
            $agent = Auth::user();
            
            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để tạo giao dịch.'
                ], 401);
            }

            // Get property and validate agent ownership
            $property = Property::where('PropertyID', $request->property_id)
                ->where('AgentID', $agent->UserID)
                ->first();
                
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bất động sản không tồn tại hoặc bạn không có quyền quản lý.'
                ], 403);
            }

            // Validate the request for 4-step workflow
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|exists:properties,PropertyID',
                'customer_id' => 'required|exists:user,UserID',
                'transaction_date' => 'required|date',
                'payment_method' => 'required|in:cash,bank',
                'rental_months' => 'required_if:transaction_type,Rent|integer|min:1',
                'payment_type' => 'required_if:transaction_type,Rent|in:monthly,quarterly,yearly,advance',
                'monthly_price' => 'required_if:transaction_type,Rent|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'contract_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
                'notes' => 'nullable|string|max:1000'
            ]);

            // Additional validation for rental properties
            if ($property->TypePro === 'Rent') {
                $validator->sometimes(['rental_months', 'payment_type', 'monthly_price'], 'required', function ($input) {
                    return true;
                });
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate customer exists and has correct role
            $customer = User::where('UserID', $request->customer_id)
                ->where('Role', 'Customer')
                ->first();
                
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khách hàng không tồn tại.'
                ], 404);
            }

            // Begin database transaction
            DB::beginTransaction();

            // Create transaction with 4-step workflow data
            $transaction = new Transaction();
            $transaction->PropertyID = $request->property_id;
            $transaction->AgentID = $agent->UserID;
            $transaction->OwnerID = $property->OwnerID;
            $transaction->CusID = $request->customer_id;
            $transaction->TotalPrice = $request->total_price;
            $transaction->TransactionDate = $request->transaction_date;
            $transaction->TransactionType = $property->TypePro; // Use property type
            
            // Set status based on payment method
            if ($request->payment_method === 'cash') {
                $transaction->TranStatus = 'Paid'; // Cash payment is immediate
            } else {
                $transaction->TranStatus = 'Pending'; // Bank transfer needs confirmation
            }
            
            $transaction->save();

            // Get the generated TransactionID
            $transactionId = $transaction->TransactionID;

            // Handle document uploads with new path structure
            if ($request->hasFile('contract_documents')) {
                // Create transaction-specific directory
                $documentPath = 'storage/document/' . $transactionId;
                $fullPath = public_path($documentPath);
                
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                foreach ($request->file('contract_documents') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $documentPath . '/' . $fileName;
                    
                    // Move file to public directory
                    $file->move($fullPath, $fileName);
                    
                    // Save document record in database
                    $document = new Document();
                    $document->TransactionID = $transactionId;
                    $document->FilePath = $filePath;
                    $document->DocumentType = $file->getClientMimeType();
                    $document->UploadedDate = now();
                    $document->save();
                }
            }

            // Create detail transaction record for rental properties
            if ($property->TypePro === 'Rent' && $request->filled('rental_months')) {
                DB::table('detail_transaction')->insert([
                    'TransactionID' => $transactionId,
                    'Num_Pay' => 1,
                    'RentalMonths' => $request->rental_months,
                    'MonthlyPrice' => $request->monthly_price,
                    'PaymentType' => $request->payment_type,
                    'PaymentMethod' => $request->payment_method
                ]);
            }

            // Calculate and create commission record
            $commissionRate = $property->TypePro === 'Rent' ? 0.05 : 0.03; // 5% for rent, 3% for sale
            $commissionAmount = $request->total_price * $commissionRate;

            DB::table('commission')->insert([
                'TransactionID' => $transactionId,
                'AgentID' => $agent->UserID,
                'Amount' => $commissionAmount,
                'Percentage' => $commissionRate * 100,
                'TypeCom' => $property->TypePro,
                'StatusCommission' => 'Pending',
                'CommissionDate' => now()
            ]);

            // Add notes if provided
            if ($request->filled('notes')) {
                DB::table('transactionlog')->insert([
                    'TransactionID' => $transactionId,
                    'Action' => 'Transaction Created',
                    'Description' => $request->notes,
                    'LogDate' => now(),
                    'UserID' => $agent->UserID
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Refresh transaction to get any trigger-updated values
            $transaction->refresh();

            // Send email notifications (optional)
            try {
                // You can implement email notifications here
                // Mail::to($customer->Email)->send(new TransactionCreated($transaction));
                // Mail::to($property->owner->Email)->send(new TransactionCreated($transaction));
            } catch (\Exception $e) {
                Log::warning('Failed to send transaction notification emails: ' . $e->getMessage());
            }

            // Prepare response message based on payment method
            $message = 'Giao dịch đã được tạo thành công!';
            if ($request->payment_method === 'cash') {
                $message .= ' Thanh toán tiền mặt đã được xác nhận.';
            } else {
                $message .= ' Vui lòng hoàn tất chuyển khoản để hoàn thành giao dịch.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'transaction' => [
                    'id' => $transaction->TransactionID,
                    'type' => $transaction->TransactionType ?? $property->TypePro,
                    'total_price' => number_format($transaction->TotalPrice, 0, ',', '.') . ' VND',
                    'property_title' => $property->Title,
                    'status' => $transaction->TranStatus,
                    'payment_method' => $request->payment_method,
                    'documents_uploaded' => $request->hasFile('contract_documents') ? count($request->file('contract_documents')) : 0,
                    'commission_amount' => number_format($commissionAmount, 0, ',', '.') . ' VND'
                ]
            ]);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            
            Log::error('Error creating transaction: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo giao dịch. Vui lòng thử lại.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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

    /**
     * Update transaction details
     */
    public function update(Request $request, $id)
    {
        try {
            $agent = Auth::user();
            
            // Get transaction and validate agent ownership
            $transaction = Transaction::with(['property'])
                ->whereHas('property', function($query) use ($agent) {
                    $query->where('AgentID', $agent->UserID);
                })
                ->where('TransactionID', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch hoặc bạn không có quyền truy cập.'
                ], 404);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:user,UserID',
                'total_price' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'payment_method' => 'required|in:cash,bank_transfer',
                'description' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if customer exists and has role Customer
            $customer = User::where('UserID', $request->customer_id)
                ->where('Role', 'Customer')
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khách hàng không tồn tại hoặc không hợp lệ.'
                ], 404);
            }

            // Begin database transaction
            DB::beginTransaction();

            // Store old values for logging
            $oldValues = [
                'customer_id' => $transaction->CusID,
                'total_price' => $transaction->TotalPrice,
                'transaction_date' => $transaction->TransactionDate,
                'description' => $transaction->Description
            ];

            // Update transaction
            $transaction->CusID = $request->customer_id;
            $transaction->TotalPrice = $request->total_price;
            $transaction->TransactionDate = $request->transaction_date;
            $transaction->Description = $request->description;
            
            // Update status based on payment method if transaction is not yet paid
            if ($transaction->TranStatus === 'Pending') {
                if ($request->payment_method === 'cash') {
                    $transaction->TranStatus = 'Paid';
                } else {
                    $transaction->TranStatus = 'Pending';
                }
            }

            $transaction->save();

            // Update commission amount if price changed
            if ($oldValues['total_price'] != $request->total_price) {
                $property = $transaction->property;
                $commissionRate = $property->TypePro === 'Rent' ? 0.05 : 0.03; // 5% for rent, 3% for sale
                $newCommissionAmount = $request->total_price * $commissionRate;

                DB::table('commission')
                    ->where('TransactionID', $id)
                    ->update([
                        'Amount' => $newCommissionAmount,
                        'Percentage' => $commissionRate * 100
                    ]);
            }

            // Log the changes
            $changes = [];
            foreach ($oldValues as $field => $oldValue) {
                $newValue = $request->input($field);
                if ($field === 'customer_id') {
                    $newValue = $request->customer_id;
                }
                
                if ($oldValue != $newValue) {
                    $changes[] = ucfirst(str_replace('_', ' ', $field)) . " changed";
                }
            }

            if (!empty($changes)) {
                DB::table('transactionlog')->insert([
                    'TransactionID' => $id,
                    'Action' => 'Transaction Updated',
                    'Description' => 'Updated: ' . implode(', ', $changes),
                    'LogDate' => now(),
                    'UserID' => $agent->UserID
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Reload transaction with relationships for response
            $transaction->load(['property', 'trans_cus', 'trans_owner']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giao dịch thành công!',
                'transaction' => [
                    'id' => $transaction->TransactionID,
                    'property_title' => $transaction->property->Title ?? '',
                    'customer_name' => $transaction->trans_cus->Name ?? '',
                    'customer_phone' => $transaction->trans_cus->Phone ?? '',
                    'owner_name' => $transaction->trans_owner->Name ?? '',
                    'amount' => $transaction->TotalPrice,
                    'formatted_amount' => number_format($transaction->TotalPrice, 0, ',', '.') . ' VND',
                    'status' => $transaction->TranStatus,
                    'type' => $transaction->TransactionType,
                    'description' => $transaction->Description ?? '',
                    'transaction_date' => $transaction->TransactionDate ? date('d/m/Y', strtotime($transaction->TransactionDate)) : '',
                    'payment_method' => $request->payment_method,
                    'updated_at' => now()->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            
            Log::error('Error updating transaction: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giao dịch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus(Request $request, $id)
    {
        try {
            $agent = Auth::user();
            
            $transaction = Transaction::with(['property'])
                ->whereHas('property', function($query) use ($agent) {
                    $query->where('AgentID', $agent->UserID);
                })
                ->where('TransactionID', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch hoặc bạn không có quyền truy cập.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Paid,Pending,Cancelled,Completed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldStatus = $transaction->TranStatus;
            $newStatus = $request->status;

            $transaction->TranStatus = $newStatus;
            $transaction->save();

            // Log the status change
            DB::table('transactionlog')->insert([
                'TransactionID' => $id,
                'Action' => 'Status Changed',
                'Description' => "Status changed from {$oldStatus} to {$newStatus}",
                'LogDate' => now(),
                'UserID' => $agent->UserID
            ]);

            // Update commission status if transaction is completed/paid
            if ($newStatus === 'Paid' || $newStatus === 'Completed') {
                DB::table('commission')
                    ->where('TransactionID', $id)
                    ->update(['StatusCommission' => 'Success']);
            } elseif ($newStatus === 'Cancelled') {
                DB::table('commission')
                    ->where('TransactionID', $id)
                    ->update(['StatusCommission' => 'Cancelled']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái giao dịch thành công',
                'transaction' => [
                    'id' => $transaction->TransactionID,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating transaction status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái giao dịch'
            ], 500);
        }
    }

    /**
     * Get transaction documents
     */
    public function getTransactionDocuments($id)
    {
        try {
            $agent = Auth::user();
            
            $transaction = Transaction::with(['property', 'document'])
                ->whereHas('property', function($query) use ($agent) {
                    $query->where('AgentID', $agent->UserID);
                })
                ->where('TransactionID', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            $documents = $transaction->document->map(function($doc) {
                return [
                    'id' => $doc->DocumentID,
                    'name' => basename($doc->FilePath),
                    'path' => asset($doc->FilePath),
                    'type' => $doc->DocumentType,
                    'uploaded_date' => $doc->UploadedDate,
                    'size' => file_exists(public_path($doc->FilePath)) ? filesize(public_path($doc->FilePath)) : 0
                ];
            });

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction documents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải tài liệu'
            ], 500);
        }
    }

    /**
     * Upload additional documents to existing transaction
     */
    public function uploadTransactionDocuments(Request $request, $id)
    {
        try {
            $agent = Auth::user();
            
            $transaction = Transaction::with(['property'])
                ->whereHas('property', function($query) use ($agent) {
                    $query->where('AgentID', $agent->UserID);
                })
                ->where('TransactionID', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $uploadedFiles = [];

            if ($request->hasFile('documents')) {
                $documentPath = 'storage/document/' . $id;
                $fullPath = public_path($documentPath);
                
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                foreach ($request->file('documents') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $documentPath . '/' . $fileName;
                    
                    $file->move($fullPath, $fileName);
                    
                    $document = new Document();
                    $document->TransactionID = $id;
                    $document->FilePath = $filePath;
                    $document->DocumentType = $file->getClientMimeType();
                    $document->UploadedDate = now();
                    $document->save();

                    $uploadedFiles[] = [
                        'id' => $document->DocumentID,
                        'name' => $fileName,
                        'path' => asset($filePath),
                        'type' => $document->DocumentType
                    ];
                }
            }

            // Log the document upload
            DB::table('transactionlog')->insert([
                'TransactionID' => $id,
                'Action' => 'Documents Uploaded',
                'Description' => count($uploadedFiles) . ' documents uploaded',
                'LogDate' => now(),
                'UserID' => $agent->UserID
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tải tài liệu thành công',
                'documents' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading transaction documents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải tài liệu'
            ], 500);
        }
    }

    /**
     * Delete transaction document
     */
    public function deleteTransactionDocument($transactionId, $documentId)
    {
        try {
            $agent = Auth::user();
            
            $transaction = Transaction::with(['property'])
                ->whereHas('property', function($query) use ($agent) {
                    $query->where('AgentID', $agent->UserID);
                })
                ->where('TransactionID', $transactionId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            $document = Document::where('DocumentID', $documentId)
                ->where('TransactionID', $transactionId)
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài liệu'
                ], 404);
            }

            // Delete physical file
            $filePath = public_path($document->FilePath);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete database record
            $document->delete();

            // Log the deletion
            DB::table('transactionlog')->insert([
                'TransactionID' => $transactionId,
                'Action' => 'Document Deleted',
                'Description' => 'Document ' . basename($document->FilePath) . ' deleted',
                'LogDate' => now(),
                'UserID' => $agent->UserID
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Xóa tài liệu thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting transaction document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa tài liệu'
            ], 500);
        }
    }

    /**
     * Get transaction analytics for agent dashboard
     */
    public function getTransactionAnalytics(Request $request)
    {
        try {
            $agent = Auth::user();
            
            // Date range (default to current month)
            $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

            $baseQuery = Transaction::with(['property'])
                ->whereHas('property', function($query) use ($agent) {
                    $query->where('AgentID', $agent->UserID);
                })
                ->whereBetween('TransactionDate', [$fromDate, $toDate]);

            // Basic statistics
            $totalTransactions = $baseQuery->count();
            $totalValue = $baseQuery->sum('TotalPrice');
            $completedTransactions = (clone $baseQuery)->whereIn('TranStatus', ['Paid', 'Completed'])->count();
            $pendingTransactions = (clone $baseQuery)->where('TranStatus', 'Pending')->count();

            // Transaction by type
            $saleTransactions = (clone $baseQuery)->where('TransactionType', 'Sale')->count();
            $rentTransactions = (clone $baseQuery)->where('TransactionType', 'Rent')->count();

            // Commission statistics
            $commissionData = DB::table('commission')
                ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
                ->join('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->where('properties.AgentID', $agent->UserID)
                ->whereBetween('transactions.TransactionDate', [$fromDate, $toDate])
                ->selectRaw('
                    SUM(commission.Amount) as total_commission,
                    SUM(CASE WHEN commission.StatusCommission = "Success" THEN commission.Amount ELSE 0 END) as paid_commission,
                    SUM(CASE WHEN commission.StatusCommission = "Pending" THEN commission.Amount ELSE 0 END) as pending_commission
                ')
                ->first();

            // Monthly trend (last 6 months)
            $monthlyTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->startOfMonth()->format('Y-m-d');
                $monthEnd = $date->endOfMonth()->format('Y-m-d');
                
                $monthData = Transaction::with(['property'])
                    ->whereHas('property', function($query) use ($agent) {
                        $query->where('AgentID', $agent->UserID);
                    })
                    ->whereBetween('TransactionDate', [$monthStart, $monthEnd])
                    ->selectRaw('
                        COUNT(*) as count,
                        SUM(TotalPrice) as total_value,
                        SUM(CASE WHEN TranStatus IN ("Paid", "Completed") THEN TotalPrice ELSE 0 END) as completed_value
                    ')
                    ->first();

                $monthlyTrend[] = [
                    'month' => $date->format('M Y'),
                    'count' => $monthData->count ?? 0,
                    'total_value' => $monthData->total_value ?? 0,
                    'completed_value' => $monthData->completed_value ?? 0
                ];
            }

            return response()->json([
                'success' => true,
                'analytics' => [
                    'period' => [
                        'from' => $fromDate,
                        'to' => $toDate
                    ],
                    'summary' => [
                        'total_transactions' => $totalTransactions,
                        'total_value' => $totalValue,
                        'completed_transactions' => $completedTransactions,
                        'pending_transactions' => $pendingTransactions,
                        'completion_rate' => $totalTransactions > 0 ? round(($completedTransactions / $totalTransactions) * 100, 1) : 0,
                        'sale_transactions' => $saleTransactions,
                        'rent_transactions' => $rentTransactions
                    ],
                    'commission' => [
                        'total' => $commissionData->total_commission ?? 0,
                        'paid' => $commissionData->paid_commission ?? 0,
                        'pending' => $commissionData->pending_commission ?? 0
                    ],
                    'monthly_trend' => $monthlyTrend
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction analytics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thống kê'
            ], 500);
        }
    }

    /**
     * Get available properties for agent's transaction creation modal
     */
    public function getAvailableProperties()
    {
        try {
            $agent = Auth::user();
            
            if (!$agent || $agent->Role !== 'Agent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent not authenticated'
                ], 401);
            }

            // Get properties assigned to current agent with active status and Sale/Rent types
            $properties = Property::with(['danhMuc', 'chiTiet', 'images'])
                ->where('AgentID', $agent->UserID)
                ->whereIn('Status', ['active', 'Active'])
                ->whereIn('TypePro', ['Sale', 'Rent'])
                ->orderBy('Title', 'asc')
                ->get();

            // Format response data
            $formattedProperties = $properties->map(function($property) {
                // Get main image
                $mainImage = null;
                $thumbnailImage = $property->images->where('IsThumbnail', 1)->first();
                $firstImage = $property->images->first();
                
                if ($thumbnailImage) {
                    $mainImage = $thumbnailImage->ImageURL ?: 
                        ($thumbnailImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($thumbnailImage->ImagePath) : null);
                } elseif ($firstImage) {
                    $mainImage = $firstImage->ImageURL ?: 
                        ($firstImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($firstImage->ImagePath) : null);
                }

                return [
                    'PropertyID' => $property->PropertyID,
                    'PropertyName' => $property->Title,
                    'Address' => $property->Address . ', ' . $property->Ward . ', ' . $property->District,
                    'Price' => $property->Price,
                    'RentalPrice' => $property->TypePro === 'Rent' ? $property->Price : null,
                    'PropertyType' => $property->danhMuc ? $property->danhMuc->ten_pro : 'Loại BĐS #' . $property->PropertyType,
                    'Status' => $property->Status,
                    'TypePro' => $property->TypePro,
                    'MainImage' => $mainImage,
                    'Area' => $property->chiTiet ? 
                        ($property->chiTiet->HouseLength && $property->chiTiet->HouseWidth 
                            ? $property->chiTiet->HouseLength * $property->chiTiet->HouseWidth 
                            : null) : null,
                    'Bedroom' => $property->chiTiet ? $property->chiTiet->Bedroom : null,
                    'Bathroom' => $property->chiTiet ? $property->chiTiet->Bath_WC : null,
                ];
            });

            return response()->json($formattedProperties);

        } catch (\Exception $e) {
            Log::error('Error getting available properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách bất động sản'
            ], 500);
        }
    }

    /**
     * Get available customers for agent's transaction creation modal
     */
    public function getAvailableCustomers()
    {
        try {
            $agent = Auth::user();
            
            if (!$agent || $agent->Role !== 'Agent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent not authenticated'
                ], 401);
            }

            // Get all customers with active status
            $customers = User::where('Role', 'Customer')
                ->where('StatusUser', 'active')
                ->orderBy('Name', 'asc')
                ->get();

            // Format response data
            $formattedCustomers = $customers->map(function($customer) {
                return [
                    'UserID' => $customer->UserID,
                    'Name' => $customer->Name,
                    'Email' => $customer->Email,
                    'Phone' => $customer->Phone,
                    'Address' => $customer->Address,
                ];
            });

            return response()->json($formattedCustomers);

        } catch (\Exception $e) {
            Log::error('Error getting available customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách khách hàng'
            ], 500);
        }
    }
}
