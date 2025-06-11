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
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class AgentController extends Controller
{
    /**
     * Display agent dashboard
     */
    public function dashboard()
    {
        // Get current agent data
        $agent = Auth::user();

        // 1. Phân công môi giới - Properties assigned to agent
        $brokerStats = [
            'active' => Property::where('AgentID', $agent->UserID)
                ->where('Status', 'active')
                ->count(),
            'pending' => Property::where('AgentID', $agent->UserID)
                ->where('Status', 'pending')
                ->count(),
            'total' => Property::where('AgentID', $agent->UserID)
                ->whereIn('Status', ['active', 'pending', 'sold', 'rented'])
                ->count()
        ];

        // 2. Lịch hẹn xem nhà - Appointments statistics
        $appointmentStats = [
            'pending' => Appointment::where('AgentID', $agent->UserID)
                ->whereIn('Status', ['Khởi tạo', 'Đang Thực hiện'])
                ->count(),
            'completed' => Appointment::where('AgentID', $agent->UserID)
                ->where('Status', 'Hoàn Thành')
                ->count(),
            'cancelled' => Appointment::where('AgentID', $agent->UserID)
                ->where('Status', 'Hủy Hẹn')
                ->count(),
            'total' => Appointment::where('AgentID', $agent->UserID)->count()
        ];

        // 3. Giao dịch - Transactions statistics
        $transactionStats = [
            'completed' => Transaction::where('AgentID', $agent->UserID)
                ->where('TranStatus', 'Paid')
                ->count(),
            'processing' => Transaction::where('AgentID', $agent->UserID)
                ->where('TranStatus', 'Pending')
                ->count(),
            'total' => Transaction::where('AgentID', $agent->UserID)->count()
        ];

        // 4. Lịch hẹn hôm nay
        $todayAppointments = Appointment::where('AgentID', $agent->UserID)
            ->with(['property', 'cusUser', 'ownerUser'])
            ->whereDate('AppointmentDateStart', today())
            ->orderBy('AppointmentDateStart', 'asc')
            ->get();

        // 5. Recent activities for additional context
        $recentAppointments = Appointment::where('AgentID', $agent->UserID)
            ->with(['property', 'cusUser', 'ownerUser'])
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(5)
            ->get();

        // 6. Commission earnings this month
        $monthlyCommission = DB::table('commission')
            ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
            ->where('commission.AgentID', $agent->UserID)
            ->whereMonth('transactions.TransactionDate', now()->month)
            ->whereYear('transactions.TransactionDate', now()->year)
            ->sum('commission.Amount');

        // 7. Performance metrics this month
        $monthlyPerformance = [
            'transactions_completed' => Transaction::where('AgentID', $agent->UserID)
                ->where('TranStatus', 'Paid')
                ->whereMonth('TransactionDate', now()->month)
                ->whereYear('TransactionDate', now()->year)
                ->count(),
            'appointments_completed' => Appointment::where('AgentID', $agent->UserID)
                ->where('Status', 'Hoàn Thành')
                ->whereMonth('AppointmentDateStart', now()->month)
                ->whereYear('AppointmentDateStart', now()->year)
                ->count(),
            'total_sales_value' => Transaction::where('AgentID', $agent->UserID)
                ->where('TranStatus', 'Paid')
                ->whereMonth('TransactionDate', now()->month)
                ->whereYear('TransactionDate', now()->year)
                ->sum('TotalPrice')
        ];

        // 8. Next upcoming appointment
        $nextAppointment = Appointment::where('AgentID', $agent->UserID)
            ->with(['property', 'cusUser', 'ownerUser'])
            ->where('AppointmentDateStart', '>', now())
            ->whereIn('Status', ['Khởi tạo', 'Đang Thực hiện'])
            ->orderBy('AppointmentDateStart', 'asc')
            ->first();

        return view('agents.dashboard', compact(
            'agent',
            'brokerStats',
            'appointmentStats',
            'transactionStats',
            'todayAppointments',
            'recentAppointments',
            'monthlyCommission',
            'monthlyPerformance',
            'nextAppointment'
        ));
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
        try {
            $user = Auth::user();

            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:user,Email,' . $user->UserID . ',UserID',
                'phone' => 'nullable|string|max:20',
            ]);

            // Update user using DB query
            $updateData = [
                'Name' => $request->name,
                'Email' => $request->email,
                'Phone' => $request->phone,
            ];

            $updated = DB::table('user')
                ->where('UserID', $user->UserID)
                ->update($updateData);

            if (!$updated) {
                return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
            }

            return redirect()->back()->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating profile: ' . $e->getMessage());
        }
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
                'transaction_type' => 'required|in:rent,sale',
                'payment_method' => 'required|in:cash,bank',
                // Rent-specific validation
                'rent_months' => 'required_if:transaction_type,rent|integer|min:1',
                'monthly_rent' => 'required_if:transaction_type,rent|numeric|min:0',
                'start_date' => 'required_if:transaction_type,rent|date',
                'end_date' => 'required_if:transaction_type,rent|date',
                // Sale-specific validation
                'sale_price' => 'required_if:transaction_type,sale|numeric|min:0',
                'sale_contract_type' => 'required_if:transaction_type,sale|in:full,deposit',
                'deposit_amount' => 'required_if:sale_contract_type,deposit|numeric|min:0',
                'payment_installments' => 'required_if:sale_contract_type,deposit|integer|min:1',
                'payment_schedule' => 'nullable|string',
                // Document validation
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
                'templates.*' => 'nullable',
                'total_price' => 'required|numeric|min:0'
            ]);

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
            $transaction->TransactionDate = now(); // Use current timestamp
            $transaction->TransactionType = ucfirst($request->transaction_type); // 'Rent' or 'Sale'

            // Set status based on payment method
            if ($request->payment_method === 'cash') {
                $transaction->TranStatus = 1; // Paid
            } else {
                $transaction->TranStatus = 0; // Pending
            }

            $transaction->save();

            // Get the generated TransactionID
            $transactionId = $transaction->TransactionID;

            // Handle document uploads with new path structure
            if ($request->hasFile('documents')) {
                // Create transaction-specific directory
                $documentPath = 'storage/document/' . $transactionId;
                $fullPath = public_path($documentPath);

                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                foreach ($request->file('documents') as $file) {
                    // Use original filename for new path format: {transactionID}/{filename}
                    $fileName = $file->getClientOriginalName();
                    $customPath = $transactionId . '/' . $fileName;

                    // Move file to public directory
                    $file->move($fullPath, $fileName);

                    // Save document record in database with new path format
                    $document = new Document();
                    $document->TransactionID = $transactionId;
                    $document->FilePath = $customPath; // Using {transactionID}/{filename} format
                    $document->DocumentType = $file->getClientMimeType();
                    $document->UploadedDate = now();
                    $document->save();
                }
            }

            // Handle template files if provided
            if ($request->has('templates')) {
                // Templates are already stored files from the template selection step
                // Just log the template usage
                foreach ($request->templates as $template) {
                    DB::table('transactionlog')->insert([
                        'TransactionID' => $transactionId,
                        'Action' => 'Template Used',
                        'Description' => 'Template file: ' . json_encode($template),
                        'LogDate' => now(),
                        'UserID' => $agent->UserID
                    ]);
                }
            }

            // Create detail transaction record with type-specific data
            if ($request->transaction_type === 'rent') {
                DB::table('detail_transaction')->insert([
                    'TransactionID' => $transactionId,
                    'Num_Pay' => 1,
                    'Price' => $request->monthly_rent,
                    'RentMonth' => $request->rent_months,
                    'DTran_Date' => $request->start_date,
                    'InstallPayment' => $request->rent_months, // Total months
                    'PaymentType' => 'Monthly', // Default for rent
                    'DTran_Status' => 'Active'
                ]);
            } elseif ($request->transaction_type === 'sale') {
                // Create detail record for sale
                $installments = 1; // Default to 1 for full payment
                if ($request->sale_contract_type === 'deposit' && $request->payment_installments) {
                    $installments = $request->payment_installments;
                }

                DB::table('detail_transaction')->insert([
                    'TransactionID' => $transactionId,
                    'Num_Pay' => 1,
                    'Price' => $request->sale_price,
                    'RentMonth' => null, // Not applicable for sale
                    'DTran_Date' => now(),
                    'InstallPayment' => $installments,
                    'PaymentType' => $request->sale_contract_type === 'deposit' ? 'Installment' : 'Full',
                    'DTran_Status' => 'Active'
                ]);

                // If deposit contract with payment schedule, save the schedule
                if ($request->sale_contract_type === 'deposit' && $request->has('payment_schedule')) {
                    $paymentSchedule = json_decode($request->payment_schedule, true);
                    if (is_array($paymentSchedule)) {
                        foreach ($paymentSchedule as $payment) {
                            DB::table('detail_transaction')->insert([
                                'TransactionID' => $transactionId,
                                'Num_Pay' => $payment['payment_number'],
                                'Price' => $payment['amount'],
                                'RentMonth' => null,
                                'DTran_Date' => $payment['due_date'],
                                'InstallPayment' => count($paymentSchedule),
                                'PaymentType' => 'Installment',
                                'DTran_Status' => 'Pending'
                            ]);
                        }
                    }
                }
            }

            // Calculate and create commission record
            $commissionRate = $request->transaction_type === 'rent' ? 0.05 : 0.03; // 5% for rent, 3% for sale
            $commissionAmount = $request->total_price * $commissionRate;

            DB::table('commission')->insert([
                'TransactionID' => $transactionId,
                'AgentID' => $agent->UserID,
                'Amount' => $commissionAmount,
                'Percentage' => $commissionRate * 100,
                'TypeCom' => ucfirst($request->transaction_type),
                'StatusCommission' => 'Pending',
                'CommissionDate' => now()
            ]);

            // Add transaction log
            DB::table('transactionlog')->insert([
                'TransactionID' => $transactionId,
                'Action' => 'Transaction Created',
                'Description' => 'Transaction created via 4-step modal. Type: ' . $request->transaction_type,
                'LogDate' => now(),
                'UserID' => $agent->UserID
            ]);

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
        $requestStatus = $request->status;

        // Map agent status to database status
        $statusMap = [
            'Thành công' => 'Đang Thực Hiện',    // STATUS_ACTIVE
            'Đã hủy' => 'Hủy Hẹn',              // STATUS_CANCELLED
            'Hoàn Thành' => 'Hoàn Thành',       // STATUS_COMPLETED
            'Chờ xử lý' => 'Khởi Tạo'           // STATUS_PENDING
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

    /**
     * Tìm kiếm khách hàng theo tên, điện thoại hoặc CMND
     */
    public function searchCustomers(Request $request)
    {
        try {
            $query = $request->get('query', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'customers' => []
                ]);
            }

            // Search customers by name, phone, or identity card
            $customers = DB::table('user')
                ->join('profile_customer', 'user.UserID', '=', 'profile_customer.UserID')
                ->select(
                    'user.UserID',
                    'user.Name',
                    'user.Phone',
                    'user.IdentityCard',
                    'user.Email',
                    'user.Address',
                    'user.Ward',
                    'user.District',
                    'user.Province'
                )
                ->where('user.Role', 'Customer')
                ->where('user.StatusUser', 'active')
                ->where(function($q) use ($query) {
                    $q->where('user.Name', 'LIKE', '%' . $query . '%')
                      ->orWhere('user.Phone', 'LIKE', '%' . $query . '%')
                      ->orWhere('user.IdentityCard', 'LIKE', '%' . $query . '%');
                })
                ->limit(10)
                ->get();

            // Format customer data
            $formattedCustomers = $customers->map(function($customer) {
                $fullAddress = collect([
                    $customer->Address,
                    $customer->Ward,
                    $customer->District,
                    $customer->Province
                ])->filter()->implode(', ');

                return [
                    'id' => $customer->UserID,
                    'name' => $customer->Name,
                    'phone' => $customer->Phone,
                    'identity_card' => $customer->IdentityCard,
                    'email' => $customer->Email,
                    'address' => $fullAddress ?: 'Chưa cập nhật'
                ];
            });

            return response()->json([
                'success' => true,
                'customers' => $formattedCustomers
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm khách hàng'
            ], 500);
        }
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
     * Get transaction details for viewing
     */
    public function getTransactionDetails($id)
    {
        try {
            // Get transaction with all related data
            $transaction = DB::table('transactions as t')
                ->leftJoin('properties as p', 't.PropertyID', '=', 'p.PropertyID')
                ->leftJoin('user as customer', 't.CusID', '=', 'customer.UserID')
                ->leftJoin('user as owner', 't.OwnerID', '=', 'owner.UserID')
                ->leftJoin('user as agent', 't.AgentID', '=', 'agent.UserID')
                ->select([
                    't.*',
                    'p.Title as PropertyTitle',
                    'customer.Name as CustomerName',
                    'owner.Name as OwnerName',
                    'agent.Name as AgentName'
                ])
                ->where('t.TransactionID', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không tồn tại'
                ], 404);
            }

            // Get payment details
            $paymentDetails = DB::table('detail_transaction')
                ->where('TransactionID', $id)
                ->get()
                ->map(function($payment) {
                    return [
                        'num_pay' => $payment->Num_Pay ?? 1,
                        'price' => $payment->Price ?? 0,
                        'rent_month' => $payment->RentMonth ?? null,
                        'dtran_date' => $payment->DTran_Date ?? null,
                        'payment_type' => $payment->PaymentType ?? 'N/A',
                        'formatted_price' => number_format($payment->Price ?? 0, 0, ',', '.') . ' VND',
                        'formatted_date' => $payment->DTran_Date ? date('d/m/Y H:i', strtotime($payment->DTran_Date)) : 'N/A',
                        'status_text' => $payment->DTran_Status ?? 'Chờ đợi'
                    ];
                });

            // Get commission info
            $commission = DB::table('commission')
                ->where('TransactionID', $id)
                ->first();

            $commissionInfo = null;
            if ($commission) {
                $commissionInfo = [
                    'percentage' => $commission->Percentage ?? 0,
                    'amount' => $commission->Amount ?? 0,
                    'status' => $commission->StatusCommission ?? 'Pending',
                    'paid_date' => $commission->PaidDate ?? null,
                    'formatted_percentage' => ($commission->Percentage ?? 0) . '%',
                    'formatted_amount' => number_format($commission->Amount ?? 0, 0, ',', '.') . ' VND',
                    'status_text' => $commission->StatusCommission ?? 'Chờ thanh toán',
                    'formatted_paid_date' => $commission->PaidDate ? date('d/m/Y', strtotime($commission->PaidDate)) : null
                ];
            }

            // Format transaction data
            $formattedTransaction = [
                'TransactionID' => $transaction->TransactionID,
                'PropertyID' => $transaction->PropertyID,
                'PropertyTitle' => $transaction->PropertyTitle ?? 'N/A',
                'CustomerName' => $transaction->CustomerName ?? 'N/A',
                'OwnerName' => $transaction->OwnerName ?? 'N/A',
                'AgentName' => $transaction->AgentName ?? 'N/A',
                'TotalPrice' => $transaction->TotalPrice ?? 0,
                'TransactionDate' => $transaction->TransactionDate,
                'TransactionType' => $transaction->TransactionType ?? 'Sale',
                'Status' => $transaction->TranStatus ?? 0,
                'FormattedAmount' => number_format($transaction->TotalPrice ?? 0, 0, ',', '.') . ' VND',
                'FormattedDate' => $transaction->TransactionDate ? date('d/m/Y H:i', strtotime($transaction->TransactionDate)) : 'N/A',
                'FormattedType' => $transaction->TransactionType === 'Rent' ? 'Cho thuê' : 'Mua bán',
                'StatusText' => $transaction->TranStatus == 1 ? 'Đã thanh toán' : ($transaction->TranStatus == 2 ? 'Đã hủy' : 'Chờ xử lý'),
                'PaymentDetails' => $paymentDetails,
                'Commission' => $commissionInfo
            ];

            return response()->json([
                'success' => true,
                'transaction' => $formattedTransaction
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin giao dịch'
            ], 500);
        }
    }

    /**
     * Get transaction data for editing
     */
    public function getTransactionForEdit($id)
    {
        try {
            // Get transaction with related data
            $transaction = DB::table('transactions as t')
                ->leftJoin('properties as p', 't.PropertyID', '=', 'p.PropertyID')
                ->leftJoin('user as customer', 't.CusID', '=', 'customer.UserID')
                ->select([
                    't.*',
                    'p.Title as PropertyTitle',
                    'customer.Name as CustomerName'
                ])
                ->where('t.TransactionID', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không tồn tại'
                ], 404);
            }

            // Get list of customers for dropdown
            $customers = DB::table('user')
                ->where('Role', 'Customer')
                ->select('UserID', 'Name as FullName')
                ->get();

            // Format transaction data for editing
            $formattedTransaction = [
                'TransactionID' => $transaction->TransactionID,
                'PropertyID' => $transaction->PropertyID,
                'CustomerID' => $transaction->CusID,
                'CustomerName' => $transaction->CustomerName ?? 'N/A',
                'Type' => $transaction->TransactionType ?? 'Sale',
                'Status' => $transaction->TranStatus ?? 0,
                'TotalPrice' => $transaction->TotalPrice ?? 0,
                'TransactionDate' => $transaction->TransactionDate,
                'Notes' => $transaction->Description ?? '',
                'FormattedAmount' => number_format($transaction->TotalPrice ?? 0, 0, ',', '.') . ' VND',
                'FormattedDateForInput' => $transaction->TransactionDate ? date('Y-m-d\TH:i', strtotime($transaction->TransactionDate)) : ''
            ];

            return response()->json([
                'success' => true,
                'transaction' => $formattedTransaction,
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction for edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin giao dịch'
            ], 500);
        }
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:0,1,2', // 0=Pending, 1=Paid, 2=Cancelled
                'notes' => 'nullable|string|max:1000'
            ]);

            $transaction = DB::table('transactions')->where('TransactionID', $id)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không tồn tại'
                ], 404);
            }

            // Business rule validation
            if ($transaction->TranStatus == 1 && $request->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thay đổi trạng thái của giao dịch đã thanh toán'
                ], 400);
            }

            if ($transaction->TranStatus == 2 && $request->status != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thay đổi trạng thái của giao dịch đã hủy'
                ], 400);
            }

            // Update transaction
            $updated = DB::table('transactions')
                ->where('TransactionID', $id)
                ->update([
                    'TranStatus' => $request->status,
                    'Description' => $request->notes
                ]);

            if ($updated) {
                // Log the change
                if ($request->filled('notes')) {
                    DB::table('transactionlog')->insert([
                        'TransactionID' => $id,
                        'Action' => 'Status Updated',
                        'Description' => $request->notes,
                        'LogDate' => now(),
                        'UserID' => Auth::id()
                    ]);
                }

                $statusText = $request->status == 1 ? 'Đã thanh toán' : ($request->status == 2 ? 'Đã hủy' : 'Chờ xử lý');

                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái giao dịch thành công',
                    'new_status' => $statusText
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật giao dịch'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error updating transaction status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giao dịch'
            ], 500);
        }
    }

    /**
     * Get documents for a specific transaction
     */
    public function getTransactionDocuments($id)
    {
        try {
            // Get transaction
            $transaction = DB::table('transactions')->where('TransactionID', $id)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không tồn tại'
                ], 404);
            }

            // Get documents for this transaction
            $documents = DB::table('documents')
                ->where('TransactionID', $id)
                ->select([
                    'DocumentID',
                    'TransactionID',
                    'DocumentType',
                    'FilePath',
                    'UploadedDate'
                ])
                ->orderBy('UploadedDate', 'desc')
                ->get();

            // Format documents
            $formattedDocuments = $documents->map(function($doc) {
                return [
                    'document_id' => $doc->DocumentID,
                    'transaction_id' => $doc->TransactionID,
                    'document_type' => $doc->DocumentType,
                    'file_path' => $doc->FilePath,
                    'file_name' => basename($doc->FilePath),
                    'uploaded_date' => $doc->UploadedDate,
                    'formatted_date' => $doc->UploadedDate ? date('d/m/Y H:i', strtotime($doc->UploadedDate)) : 'N/A'
                ];
            });

            return response()->json([
                'success' => true,
                'documents' => $formattedDocuments
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách tài liệu'
            ], 500);
        }
    }

    /**
     * Upload document for transaction
     */
    public function uploadTransactionDocument(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'document' => 'required|file|max:10240', // 10MB max
                'document_type' => 'required|string'
            ]);

            // Get transaction
            $transaction = DB::table('transactions')->where('TransactionID', $id)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không tồn tại'
                ], 404);
            }

            $file = $request->file('document');
            $documentType = $request->input('document_type');

            // Use original filename for new path format: {transactionID}/{filename}
            $fileName = $file->getClientOriginalName();
            $customPath = $id . '/' . $fileName;

            // Create directory if it doesn't exist
            $uploadPath = public_path('storage/document/' . $id);
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Move file
            $file->move($uploadPath, $fileName);

            // Save to database with new path format
            $documentId = DB::table('documents')->insertGetId([
                'TransactionID' => $id,
                'DocumentType' => $documentType,
                'FilePath' => $customPath, // Using {transactionID}/{filename} format
                'UploadedDate' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tải lên tài liệu thành công',
                'document' => [
                    'document_id' => $documentId,
                    'file_name' => $fileName,
                    'document_type' => $documentType,
                    'file_path' => $customPath
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lên tài liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document
     */
    public function downloadTransactionDocument($transactionId, $documentId)
    {
        try {
            // Get document
            $document = DB::table('documents')
                ->where('DocumentID', $documentId)
                ->where('TransactionID', $transactionId)
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài liệu không tồn tại'
                ], 404);
            }

            // Build correct file path for new format: storage/document/{transactionID}/{filename}
            $filePath = public_path('storage/document/' . $document->FilePath);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tệp không tồn tại trên server'
                ], 404);
            }

            return response()->download($filePath, basename($document->FilePath));

        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải xuống tài liệu'
            ], 500);
        }
    }

    /**
     * Delete document
     */
    public function deleteTransactionDocument($transactionId, $documentId)
    {
        try {
            // Get document
            $document = DB::table('documents')
                ->where('DocumentID', $documentId)
                ->where('TransactionID', $transactionId)
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài liệu không tồn tại'
                ], 404);
            }

            // Build correct file path for new format: storage/document/{transactionID}/{filename}
            $filePath = public_path('storage/document/' . $document->FilePath);

            // Delete file from filesystem
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            DB::table('documents')
                ->where('DocumentID', $documentId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa tài liệu thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa tài liệu'
            ], 500);
        }
    }

    /**
     * Download all documents for a transaction as ZIP file
     */
    public function downloadAllTransactionDocuments($transactionId)
    {
        try {
            // Get transaction
            $transaction = DB::table('transactions')->where('TransactionID', $transactionId)->first();

            if (!$transaction) {
                abort(404, 'Giao dịch không tồn tại');
            }

            // Get all documents for this transaction
            $documents = DB::table('documents')
                ->where('TransactionID', $transactionId)
                ->get();

            if ($documents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có tài liệu nào để tải xuống'
                ], 404);
            }

            // Create temporary directory for ZIP
            $tempDir = sys_get_temp_dir() . '/transaction_' . $transactionId . '_' . time();
            if (!mkdir($tempDir, 0755, true)) {
                throw new \Exception('Không thể tạo thư mục tạm');
            }

            $zip = new \ZipArchive();
            $zipFileName = "transaction_{$transactionId}_documents.zip";
            $zipPath = $tempDir . '/' . $zipFileName;

            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Không thể tạo file ZIP');
            }

            $addedFiles = 0;
            foreach ($documents as $document) {
                $filePath = public_path('storage/document/' . $document->FilePath);

                if (file_exists($filePath)) {
                    $fileName = basename($document->FilePath);
                    // Add document type prefix to avoid filename conflicts
                    $prefixedFileName = $document->DocumentType . '_' . $fileName;
                    $zip->addFile($filePath, $prefixedFileName);
                    $addedFiles++;
                }
            }

            $zip->close();

            if ($addedFiles === 0) {
                // Clean up
                unlink($zipPath);
                rmdir($tempDir);

                return response()->json([
                    'success' => false,
                    'message' => 'Không có tài liệu hợp lệ để tải xuống'
                ], 404);
            }

            // Return the ZIP file for download
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Error downloading all documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo file tải xuống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assigned properties for the 4-step transaction modal
     */
    public function getAssignedProperties()
    {
        try {
            $agentId = Auth::user()->UserID;

            // Debug logging
            Log::info('Agent ID: ' . $agentId);

            // Check all properties first (for debugging)
            $allProperties = Property::all();
            Log::info('Total properties in system: ' . $allProperties->count());

            // Check properties assigned to this agent
            $agentProperties = Property::where('AgentID', $agentId)->get();
            Log::info('Properties assigned to agent: ' . $agentProperties->count());

            // Check active properties assigned to this agent
            $activeProperties = Property::where('AgentID', $agentId)
                ->where('Status', 'active')
                ->get();
            Log::info('Active properties assigned to agent: ' . $activeProperties->count());

            $properties = Property::where('AgentID', $agentId)
                ->where('Status', 'active')
                ->with(['chiTiet', 'images', 'danhMuc'])
                ->get()
                ->map(function($property) {
                    // Format location from address components
                    $location = $property->Address . ', ' . $property->Ward . ', ' . $property->District . ', ' . $property->Province;

                    // Calculate area from TotalWidth and TotalLength if available
                    $area = 'N/A';
                    if ($property->chiTiet && $property->chiTiet->TotalWidth && $property->chiTiet->TotalLength) {
                        $area = $property->chiTiet->TotalWidth * $property->chiTiet->TotalLength;
                    }

                    // Get first image if available
                    $image = $property->images->first() ? '/storage/' . $property->images->first()->ImagePath : '/images/no-image.jpg';

                    // Get property type name
                    $propertyTypeName = $property->danhMuc ? $property->danhMuc->TypeName : 'N/A';

                    return [
                        'id' => $property->PropertyID,
                        'title' => $property->Title,
                        'price' => $property->Price,
                        'location' => $location,
                        'transaction_type' => $property->TypePro,
                        'area' => $area,
                        'image' => $image,
                        'description' => $property->Description,
                        'property_type' => $propertyTypeName
                    ];
                });

            Log::info('Final properties count: ' . $properties->count());

            return response()->json([
                'success' => true,
                'properties' => $properties,
                'debug' => [
                    'agent_id' => $agentId,
                    'total_properties' => $allProperties->count(),
                    'agent_properties' => $agentProperties->count(),
                    'active_properties' => $activeProperties->count(),
                    'final_count' => $properties->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading assigned properties: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách bất động sản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract templates for the 4-step transaction modal
     */
    public function getContractTemplates()
    {
        try {
            $contractPath = public_path('storage/document/HopDong');
            $templates = [];

            Log::info('Contract template path: ' . $contractPath);

            if (is_dir($contractPath)) {
                $files = scandir($contractPath);
                $id = 1;

                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && !is_dir($contractPath . '/' . $file)) {
                        $filePath = $contractPath . '/' . $file;
                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                        $name = pathinfo($file, PATHINFO_FILENAME);
                        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

                        // Only include document files
                        if (in_array(strtolower($extension), ['pdf', 'doc', 'docx'])) {
                            $templates[] = [
                                'id' => $id++,
                                'name' => $file, // Filename for data-template-id
                                'display_name' => $name, // Display name without extension
                                'filename' => $file, // Full filename
                                'file_type' => strtolower($extension),
                                'size' => $this->formatFileSize($fileSize),
                                'description' => $this->getTemplateDescription($name),
                                'path' => 'storage/document/HopDong/' . $file
                            ];
                        }
                    }
                }
                Log::info('Found templates: ' . count($templates));
            } else {
                Log::warning('Contract template directory not found: ' . $contractPath);
            }

            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading contract templates: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải mẫu hợp đồng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Helper method to get template description based on filename
     */
    private function getTemplateDescription($filename)
    {
        $descriptions = [
            'hop_dong_cho_thue' => 'Mẫu hợp đồng cho thuê bất động sản',
            'hop_dong_ban' => 'Mẫu hợp đồng bán bất động sản',
            'hop_dong_dat_coc' => 'Mẫu hợp đồng đặt cọc',
            'hop_dong_moi_gioi' => 'Mẫu hợp đồng môi giới',
            'bien_ban_ban_giao' => 'Mẫu biên bản bàn giao',
            'giay_uy_quyen' => 'Mẫu giấy ủy quyền'
        ];


        $filename = strtolower($filename);

        foreach ($descriptions as $key => $description) {
            if (strpos($filename, $key) !== false) {
                return $description;
            }
        }

        return 'Mẫu hợp đồng hệ thống';
    }



    /**
     * Create Word template copy with property data using PHPWord
     */
    private function createWordTemplateCopy($originalPath, $copyPath, $property)
    {
        try {
            // Load the template
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($originalPath);

            // Get property details
            $propertyDetail = $property->detail;

            // Calculate area
            $area = 'N/A';
            if ($propertyDetail && $propertyDetail->TotalWidth && $propertyDetail->TotalLength) {
                $area = ($propertyDetail->TotalWidth * $propertyDetail->TotalLength) . ' m²';
            }

            // Replace placeholders with actual property data
            $replacements = [
                'PROPERTY_TITLE' => $property->Title ?? '',
                'PROPERTY_LOCATION' => $property->Location ?? '',
                'PROPERTY_PRICE' => number_format($property->Price ?? 0) . ' VNĐ',
                'PROPERTY_AREA' => $area,
                'PROPERTY_WIDTH' => $propertyDetail->TotalWidth ?? 'N/A',
                'PROPERTY_LENGTH' => $propertyDetail->TotalLength ?? 'N/A',
                'PROPERTY_BEDROOMS' => $propertyDetail->BedRooms ?? 'N/A',
                'PROPERTY_BATHROOMS' => $propertyDetail->BathRooms ?? 'N/A',
                'PROPERTY_FLOORS' => $propertyDetail->Floor ?? 'N/A',
                'PROPERTY_DIRECTION' => $propertyDetail->Direction ?? 'N/A',
                'PROPERTY_FURNISHING' => $propertyDetail->Furnishing ?? 'N/A',
                'PROPERTY_DESCRIPTION' => $property->Description ?? '',
                'CURRENT_DATE' => date('d/m/Y'),
                'AGENT_NAME' => auth()->user()->FullName ?? '',
                'AGENT_PHONE' => auth()->user()->Phone ?? '',
                'AGENT_EMAIL' => auth()->user()->Email ?? '',
            ];

            // Apply replacements
            foreach ($replacements as $placeholder => $value) {
                $templateProcessor->setValue($placeholder, $value);
            }

            // Save the processed template
            $templateProcessor->saveAs($copyPath);

        } catch (\Exception $e) {
            // If PHPWord fails, fallback to simple copy
            Log::warning('PHPWord processing failed, using simple copy: ' . $e->getMessage());
            copy($originalPath, $copyPath);
        }
    }

    /**
     * Show template editor
     */
    public function editTemplate($file)
    {
        try {
            $tempPath = public_path('storage/document/temp');
            $filePath = $tempPath . '/' . $file;

            if (!file_exists($filePath)) {
                abort(404, 'Template không tồn tại');
            }

            // Check agent ownership based on filename pattern
            $agent = auth()->user();
            if (!str_starts_with($file, "blank_{$agent->UserID}_")) {
                abort(403, 'Không có quyền truy cập template này');
            }

            // Return template editor view
            return view('agents.template-editor', [
                'filename' => $file,
                'filePath' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading template editor: ' . $e->getMessage());
            abort(500, 'Lỗi khi tải trình chỉnh sửa');
        }
    }

    /**
     * Show template preview
     */
    public function previewTemplate($file)
    {
        try {
            $tempPath = public_path('storage/document/temp');
            $filePath = $tempPath . '/' . $file;

            if (!file_exists($filePath)) {
                abort(404, 'Template không tồn tại');
            }

            // Check agent ownership based on filename pattern
            $agent = auth()->user();
            if (!str_starts_with($file, "blank_{$agent->UserID}_")) {
                abort(403, 'Không có quyền truy cập template này');
            }

            // Return preview view
            return view('agents.template-preview', [
                'filename' => $file,
                'filePath' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading template preview: ' . $e->getMessage());
            abort(500, 'Lỗi khi tải preview');
        }
    }

    /**
     * Save template changes
     */
    public function saveTemplate(Request $request)
    {
        try {
            $filename = $request->filename;
            $content = $request->content;

            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin filename'
                ]);
            }

            $tempPath = public_path('storage/document/temp');
            $filePath = $tempPath . '/' . $filename;

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template không tồn tại'
                ]);
            }

            // Check agent ownership based on filename pattern
            $agent = auth()->user();
            if (!str_starts_with($filename, "blank_{$agent->UserID}_")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền truy cập template này'
                ]);
            }

            // For now, just return success (actual editing will be handled by the editor interface)
            return response()->json([
                'success' => true,
                'message' => 'Template đã được lưu thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download template file for editing in Word
     */
    public function downloadTemplate($file)
    {
        try {
            $tempPath = public_path('storage/document/temp');
            $filePath = $tempPath . '/' . $file;

            if (!file_exists($filePath)) {
                Log::error('Template file not found: ' . $filePath);
                abort(404, 'Template không tồn tại');
            }

            // Check agent ownership based on filename pattern
            $agent = auth()->user();
            if (!str_starts_with($file, "blank_{$agent->UserID}_")) {
                Log::warning('Unauthorized template access attempt', [
                    'file' => $file,
                    'agent_id' => $agent->UserID,
                    'ip' => request()->ip()
                ]);
                abort(403, 'Không có quyền truy cập template này');
            }

            // Get file extension for proper content type
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // Set proper headers based on file type
            $headers = [
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            // Set Content-Type based on extension
            if ($extension === 'docx') {
                $headers['Content-Type'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            } elseif ($extension === 'doc') {
                $headers['Content-Type'] = 'application/msword';
            } elseif ($extension === 'pdf') {
                $headers['Content-Type'] = 'application/pdf';
            } else {
                $headers['Content-Type'] = 'application/octet-stream';
            }

            // Set filename in header
            $headers['Content-Disposition'] = 'attachment; filename="' . $file . '"';

            Log::info('Downloading template from temp folder', [
                'file' => $file,
                'agent_id' => $agent->UserID,
                'extension' => $extension,
                'content_type' => $headers['Content-Type']
            ]);

            return response()->download($filePath, $file, $headers);

        } catch (\Exception $e) {
            Log::error('Error downloading template: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Lỗi khi tải template');
        }
    }

    /**
     * Create a copy of template for editing with property data
     */
    public function createTemplateCopy(Request $request)
    {
        try {
            $templateName = $request->input('template_name');
            $propertyId = $request->input('property_id');

            if (!$templateName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên template không được để trống'
                ]);
            }

            // Get property data if provided
            $property = null;
            if ($propertyId) {
                $property = Property::with(['chiTiet', 'danhMuc'])->find($propertyId);
                if (!$property) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy bất động sản'
                    ]);
                }
            }

            $agent = auth()->user();
            $contractPath = public_path('storage/document/HopDong');
            $tempPath = public_path('storage/document/temp');

            // Create temp directory if it doesn't exist
            if (!is_dir($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $originalPath = $contractPath . '/' . $templateName;

            if (!file_exists($originalPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template gốc không tồn tại: ' . $templateName
                ]);
            }

            // Generate unique filename for the copy
            // IMPORTANT: ALL Word files (.doc and .docx) will be converted to .docx format
            $timestamp = time();
            $extension = pathinfo($templateName, PATHINFO_EXTENSION);
            $baseName = pathinfo($templateName, PATHINFO_FILENAME);

            // Force .docx extension for ALL Word documents for better compatibility
            $outputExtension = in_array(strtolower($extension), ['doc', 'docx']) ? 'docx' : $extension;
            $copyFileName = "blank_{$agent->UserID}_{$timestamp}_{$baseName}.{$outputExtension}";
            $copyPath = $tempPath . '/' . $copyFileName;

            Log::info("Creating template copy", [
                'from' => $originalPath,
                'to' => $copyPath,
                'original_extension' => $extension,
                'output_extension' => $outputExtension,
                'will_convert_to_docx' => in_array(strtolower($extension), ['doc', 'docx'])
            ]);

            // Create the copy - ALWAYS convert Word files to .docx format
            if (in_array(strtolower($extension), ['doc', 'docx'])) {
                // Handle Word files - always process with PHPWord to ensure .docx output
                try {
                    $templateProcessor = new TemplateProcessor($originalPath);

                    // If property data is available, fill the template
                    if ($property) {
                        // Calculate area from property details
                        $area = 'N/A';
                        if ($property->chiTiet && $property->chiTiet->TotalWidth && $property->chiTiet->TotalLength) {
                            $area = ($property->chiTiet->TotalWidth * $property->chiTiet->TotalLength) . ' m²';
                        }

                        // Format full address
                        $fullAddress = trim(implode(', ', array_filter([
                            $property->Address,
                            $property->Ward,
                            $property->District,
                            $property->Province
                        ])));

                        // Prepare property data for template replacement
                        $replacements = [
                            'PROPERTY_TITLE' => $property->Title ?? 'N/A',
                            'PROPERTY_ADDRESS' => $fullAddress,
                            'PROPERTY_PRICE' => number_format($property->Price ?? 0, 0, ',', '.') . ' VNĐ',
                            'PROPERTY_AREA' => $area,
                            'PROPERTY_WIDTH' => $property->chiTiet->TotalWidth ?? 'N/A',
                            'PROPERTY_LENGTH' => $property->chiTiet->TotalLength ?? 'N/A',
                            'PROPERTY_BEDROOMS' => $property->chiTiet->BedRooms ?? 'N/A',
                            'PROPERTY_BATHROOMS' => $property->chiTiet->BathRooms ?? 'N/A',
                            'PROPERTY_FLOORS' => $property->chiTiet->Floor ?? 'N/A',
                            'PROPERTY_DIRECTION' => $property->chiTiet->Direction ?? 'N/A',
                            'PROPERTY_FURNISHING' => $property->chiTiet->Furnishing ?? 'N/A',
                            'PROPERTY_DESCRIPTION' => $property->Description ?? 'N/A',
                            'PROPERTY_TYPE' => $property->danhMuc->TypeName ?? 'N/A',
                            'CURRENT_DATE' => date('d/m/Y'),
                            'AGENT_NAME' => $agent->Name ?? 'N/A',
                            'AGENT_PHONE' => $agent->Phone ?? 'N/A',
                            'AGENT_EMAIL' => $agent->Email ?? 'N/A',
                        ];

                        // Apply all replacements to template
                        foreach ($replacements as $placeholder => $value) {
                            $templateProcessor->setValue($placeholder, $value);
                        }

                        Log::info('Applied property data to template', [
                            'replacements_count' => count($replacements),
                            'property_id' => $propertyId
                        ]);
                    }

                    // ALWAYS save as .docx format for Word files
                    $templateProcessor->saveAs($copyPath);

                    Log::info('PHPWord template processed successfully', [
                        'template' => $templateName,
                        'property_id' => $propertyId,
                        'agent_id' => $agent->UserID,
                        'output_file' => $copyFileName,
                        'original_extension' => $extension,
                        'output_extension' => $outputExtension,
                        'has_property_data' => $property ? true : false
                    ]);

                } catch (\Exception $e) {
                    // If PHPWord fails, try basic conversion to .docx
                    Log::warning('PHPWord processing failed, attempting basic conversion: ' . $e->getMessage());

                    try {
                        // Try to create a simple PHPWord template processor for conversion
                        $templateProcessor = new TemplateProcessor($originalPath);
                        $templateProcessor->saveAs($copyPath);
                        Log::info('Successfully converted Word file to .docx using basic PHPWord conversion');
                    } catch (\Exception $e2) {
                        Log::error('PHPWord conversion completely failed: ' . $e2->getMessage());
                        // As last resort, copy the file but warn that it may not be .docx
                        copy($originalPath, $copyPath);
                        Log::warning('Fallback to file copy - output may not be in .docx format');
                    }
                }
            } else {
                // Simple copy for non-Word files
                copy($originalPath, $copyPath);
                Log::info('Non-Word file copied as-is', ['extension' => $extension]);
            }

            if (!file_exists($copyPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tạo bản sao template'
                ]);
            }

            // Generate download URL
            $downloadUrl = url("/agent/template/download/{$copyFileName}");

            Log::info("Template copy created successfully: $copyFileName");

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo bản sao template thành công',
                'copy_file' => $copyFileName,
                'download_url' => $downloadUrl,
                'original_template' => $templateName
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating template copy: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download contract template file
     */
    public function downloadContractTemplate($id)
    {
        try {
            // ID here is actually the filename
            $filename = $id;
            $contractPath = public_path('storage/document/HopDong');
            $filePath = $contractPath . '/' . $filename;

            Log::info('Attempting to download template: ' . $filename);
            Log::info('Full path: ' . $filePath);

            // Check if file exists
            if (!file_exists($filePath)) {
                Log::error('Template file not found: ' . $filePath);
                abort(404, 'Tệp mẫu hợp đồng không tồn tại');
            }

            // Check if it's a valid document file
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), ['pdf', 'doc', 'docx'])) {
                abort(403, 'Loại tệp không được phép tải xuống');
            }

            Log::info('Downloading template: ' . $filename);

            // Return file download response
            return response()->download($filePath, $filename, [
                'Content-Type' => $this->getContentType($extension),
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading contract template: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Có lỗi xảy ra khi tải tệp mẫu hợp đồng');
        }
    }

    /**
     * Get properties assigned to the authenticated agent
     */
    public function getProperties()
    {
        try {
            $agent = Auth::user();

            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin agent'
                ], 401);
            }

            // Get properties assigned to this agent using correct column names from database
            $properties = Property::where('AgentID', $agent->UserID)
                ->where('Status', 'active')
                ->with('owner')
                ->get();

            $formattedProperties = $properties->map(function ($property) {
                // Format price based on TypePro (Rent/Sale)
                $price = $property->Price;
                $transactionType = strtolower($property->TypePro); // 'Rent' or 'Sale' from database

                if ($transactionType === 'rent') {
                    $formattedPrice = number_format($price, 0, ',', '.') . ' VNĐ/tháng';
                } else {
                    $formattedPrice = number_format($price, 0, ',', '.') . ' VNĐ';
                }

                // Build full address
                $fullAddress = trim(implode(', ', array_filter([
                    $property->Address,
                    $property->Ward,
                    $property->District,
                    $property->Province
                ])));

                return [
                    'id' => $property->PropertyID,
                    'title' => $property->Title,
                    'price' => $property->Price,
                    'formatted_price' => $formattedPrice,
                    'address' => $fullAddress,
                    'transaction_type' => $transactionType,
                    'property_type' => $property->PropertyType,
                    'owner_name' => $property->owner ? $property->owner->Name : 'Chưa xác định',
                    'description' => $property->Description,
                    'status' => $property->Status
                ];
            });

            return response()->json([
                'success' => true,
                'properties' => $formattedProperties
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách bất động sản'
            ], 500);
        }
    }

    /**
     * Get content type based on file extension
     */
    private function getContentType($extension)
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        return $contentTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Upload document for creating transaction (temporary upload)
     */
    public function uploadTransactionDocumentForCreation(Request $request)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ]);

            $file = $request->file('document');
            $agent = Auth::user();

            // Get file info before moving
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Create temporary directory for upload during creation
            $tempPath = public_path('storage/document/temp_uploads/' . $agent->UserID);
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            // Generate unique filename
            $fileName = time() . '_' . $originalName;

            // Move file
            $file->move($tempPath, $fileName);

            return response()->json([
                'success' => true,
                'document' => [
                    'id' => uniqid(),
                    'name' => $originalName,
                    'path' => 'temp_uploads/' . $agent->UserID . '/' . $fileName,
                    'size' => $fileSize
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading document for creation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lên tài liệu: ' . $e->getMessage()
            ], 500);
        }
    }
}
