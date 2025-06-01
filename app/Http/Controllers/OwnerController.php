<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\User;
use App\Models\DanhMucBDS;
use App\Models\Appointment;
use App\Models\DetailProperty;
use App\Models\Transaction;
use App\Models\Image;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function dashboard()
    {
        $owner = Auth::user();
        
        // Count of properties managed by this agent
        $propertyCount = Property::where('OwnerID', $owner->UserID)->count();        // Get recent appointments
        $recentAppointments = Appointment::where('OwnerID', $owner->UserID)
            ->with(['property', 'cusUser', 'agentUser'])
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(10)
            ->get();

        $recentProperties = Property::where('OwnerID', $owner->UserID)
            ->with('agent')
            ->orderBy('PostedDate', 'desc')
            ->limit(10)
            ->get();
        
        return view('owners.dashboard', compact('owner', 'propertyCount', 'recentAppointments', 'recentProperties'));
    }

    public function listProperty()
    {
        $ownerId = Auth::user()->UserID;
        
        $properties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->get();

        $ownerProperties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->where('Status', 'pending')
            ->get();

        $categories = DanhMucBDS::all();
        $owners = User::all();

        Log::info('Properties count: ' . $properties->count());
        Log::info('Pending properties count: ' . $ownerProperties->count());

        return view('owners.property.index', compact(
            'properties',       
            'ownerProperties', 
            'categories',
            'owners'
        ));
    }

    public function appointments()
    {
        $owner = Auth::user();
        $appointments = Appointment::with(['cusUser', 'agentUser', 'property'])
            ->where('OwnerID', $owner->UserID)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();
  
        $properties = Property::where('OwnerID', $owner->UserID)
            ->select('PropertyID', 'Title', 'Address', 'Ward', 'District')
            ->get();

        $propertyList = [];
        foreach ($properties as $property) {
            $propertyList[] = [
                'id' => $property->PropertyID,
                'title' => $property->Title,
                'address' => $property->Address ?? '',
                'district' => $property->District ?? '',
                'ward' => $property->Ward ?? '',
            ];
        }

        Appointment::where('OwnerID', $owner->UserID)
            ->where('Status', 'Đang Thực Hiện')
            ->where('AppointmentDateEnd', '<=', now())
            ->update(['Status' => 'Hoàn Thành']);

        $appointments = Appointment::with(['cusUser', 'agentUser', 'property'])
            ->where('OwnerID', $owner->UserID)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        $upcomingAppointments = $appointments->filter(function ($a) {
            return in_array($a->Status, ['Khởi Tạo', 'Đang Thực Hiện'])
                && Carbon::parse($a->AppointmentDateStart)->isAfter(now());
        })->sortBy('AppointmentDateStart');

        return view('owners.appointment.appointments', compact('appointments', 'properties', 'propertyList','upcomingAppointments'));
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        $appointment = Appointment::with(['cusUser', 'agentUser', 'property', 'ownerUser'])->findOrFail($id);
        $oldStatus = $appointment->Status;
        $newStatus = $request->status;

        $request->validate([
            'status' => 'required|string|in:Đang Thực Hiện,Hủy Hẹn'
        ]);

        if ($appointment->OwnerID !== Auth::id() || $appointment->Status !== 'Khởi Tạo') {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật lịch hẹn này');
        }

        $appointment->Status = $newStatus;
        $appointment->save();

        // Gửi thông báo cho agent (người môi giới)
        if ($appointment->agentUser) {
            $appointment->agentUser->notify(
                new \App\Notifications\AppointmentStatusChanged($appointment, $oldStatus, $newStatus, $appointment->ownerUser ? $appointment->ownerUser->Name : null)
            );
        }

        // Gửi thông báo cho khách hàng (nếu muốn)
        if ($appointment->cusUser) {
            $appointment->cusUser->notify(
                new \App\Notifications\AppointmentStatusChanged($appointment, $oldStatus, $newStatus, $appointment->ownerUser ? $appointment->ownerUser->Name : null)
            );
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái lịch hẹn thành công');
    }


    public function searchProperties(Request $request)
    {
        $owner = Auth::user();
        $searchText = $request->query('term');
        
        $property = Property::with('agent')
            ->where('OwnerID', $owner->UserID)
            ->where('Title', 'LIKE', "%{$searchText}%")
            ->first();

        if ($property) {
            return response()->json([
                'id' => $property->PropertyID,
                'title' => $property->Title,
                'agentName' => $property->agent->Name ?? 'Không xác định'
            ]);
        }

        return response()->json(null);
    }

    public function getRelatedCustomers($propertyId)
    {
        $owner = Auth::user();
        
        return User::whereHas('appoint_customer', function($query) use ($propertyId) {
                $query->where('PropertyID', $propertyId);
            })
            ->select('UserID', 'Name')
            ->get();
    }

    public function transactions(Request $request)
    {
        $ownerId = Auth::user()->UserID;

        // Process date filter
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Base query for transactions
        $query = Transaction::with(['property', 'trans_cus', 'trans_agent'])
            ->where('OwnerID', $ownerId);

        // Apply date filtering if provided
        if ($fromDate && $toDate) {
            $query->whereBetween('TransactionDate', [$fromDate, $toDate]);
        }

        // Get filtered transactions
        $transactions = $query->orderBy('TransactionDate', 'desc')->get();

        // Calculate statistics
        $totalValue = $transactions->sum('Amount');
        $transactionCount = $transactions->count();
        $paidAmount = $transactions->where('Status', 'Hoàn thành')->sum('Amount');

        // Calculate success rate
        $successRate = $transactionCount > 0
            ? round(($transactions->where('Status', 'Hoàn thành')->count() / $transactionCount) * 100)
            : 0;

        // Previous period (for comparison)
        $previousFrom = Carbon::parse($fromDate)->subMonth()->format('Y-m-d');
        $previousTo = Carbon::parse($toDate)->subMonth()->format('Y-m-d');

        $previousTransactions = Transaction::where('OwnerID', $ownerId)
            ->whereBetween('TransactionDate', [$previousFrom, $previousTo])
            ->get();

        $previousTotalValue = $previousTransactions->sum('Amount');
        $previousCount = $previousTransactions->count();
        $previousPaidAmount = $previousTransactions->where('Status', 'Hoàn thành')->sum('Amount');

        // Calculate growth percentages
        $valueGrowth = $previousTotalValue > 0
            ? round((($totalValue - $previousTotalValue) / $previousTotalValue) * 100)
            : 0;
        $countGrowth = $previousCount > 0
            ? $transactionCount - $previousCount
            : 0;
        $paidGrowth = $previousPaidAmount > 0
            ? round((($paidAmount - $previousPaidAmount) / $previousPaidAmount) * 100)
            : 0;

        return view('owners.transactions.transactions', compact(
            'transactions',
            'totalValue',
            'transactionCount',
            'paidAmount',
            'successRate',
            'valueGrowth',
            'countGrowth',
            'paidGrowth',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Hiển thị chi tiết giao dịch cụ thể.
     */
    public function showTransaction($id)
    {
        $transaction = Transaction::with(['property', 'trans_owner', 'trans_cus', 'trans_agent'])
            ->findOrFail($id);

        // Check if user has permission to view this transaction
        if (Auth::user()->role == 'owner' && Auth::user()->UserID != $transaction->OwnerID) {
            abort(403, 'Unauthorized action.');
        }

        return view('owners.transactions.show', compact('transaction'));
    }

    /**
     * In hóa đơn giao dịch.
     */
    public function printInvoice($id)
    {
        $transaction = Transaction::with(['property', 'trans_owner', 'trans_cus', 'trans_agent'])
            ->findOrFail($id);

        // Check if user has permission to view this transaction
        if (Auth::user()->role == 'owner' && Auth::user()->UserID != $transaction->OwnerID) {
            abort(403, 'Unauthorized action.');
        }

        // Generate HTML for the invoice
        $html = View::make('owners.transactions.invoice', compact('transaction'))->render();

        // Return response with headers for PDF download
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Transaction-'.$id.'.pdf"');
    }

    /**
     * Xuất danh sách giao dịch ra file CSV.
     */
    public function exportTransactions(Request $request)
    {
        $user = Auth::user();
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Base query depending on user role
        if ($user->role == 'owner') {
            $query = Transaction::with('property')
                ->where('OwnerID', $user->UserID);
        } elseif ($user->role == 'admin' || $user->role == 'agent') {
            $query = Transaction::with('property');
        } else {
            abort(403, 'Unauthorized action.');
        }

        // Apply date filtering
        $query->whereBetween('TransactionDate', [$fromDate, $toDate]);

        // Get filtered transactions
        $transactions = $query->orderBy('TransactionDate', 'desc')->get();

        // Create and return CSV/Excel file
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'ID', 'Date', 'Property', 'Type', 'Amount', 'Customer', 'Status'
            ]);

            // Add data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->TransactionID,
                    Carbon::parse($transaction->TransactionDate)->format('d/m/Y'),
                    $transaction->property ? $transaction->property->Title : 'N/A',
                    $transaction->TransactionType,
                    number_format($transaction->Amount, 0),
                    $transaction->trans_cus ? $transaction->trans_cus->FullName : 'N/A',
                    $transaction->Status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Hiển thị trang thông tin cá nhân
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('owners.profile', compact('user'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'FullName' => 'required|string|max:255',
            'PhoneNumber' => 'required|string|max:20',
            'Email' => 'required|email|max:255|unique:users,Email,'.$user->UserID.',UserID',
            'Address' => 'nullable|string|max:255',
            'Avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Xử lý upload avatar nếu có
        if ($request->hasFile('Avatar')) {
            $avatar = $request->file('Avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $filename);
            $user->Avatar = $filename;
        }

        $user->FullName = $validated['FullName'];
        $user->PhoneNumber = $validated['PhoneNumber'];
        $user->Email = $validated['Email'];
        $user->Address = $validated['Address'];
        $user->save();

        return redirect()->route('owner.profile')->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangePasswordForm()
    {
        return view('owners.change-password');
    }

    /**
     * Thực hiện đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('owner.profile')->with('success', 'Đổi mật khẩu thành công!');
    }

    public function storePropertyListing(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:property,PropertyID',
            // Thêm các validation rules khác tùy thuộc vào yêu cầu
        ]);

        // Trong thực tế, sẽ lưu tin đăng và các thông tin liên quan ở đây

        return response()->json([
            'success' => true,
            'message' => 'Tin đăng ký gửi bất động sản đã được tạo thành công!',
            'redirect' => route('owner.property.index')
        ]);
    }

    public function getPropertiesForListing()
    {
        try {
            $ownerId = Auth::user()->UserID;

            // Chỉ lấy BĐS của owner hiện tại và có status là pending
            $ownerProperties = Property::with(['danhMuc', 'chiTiet', 'images'])
                ->where('OwnerID', $ownerId)
                ->where('Status', 'pending')
                ->get();

            Log::info('Found ' . $ownerProperties->count() . ' pending properties for owner ' . $ownerId);

            // Transform data to include required fields
            $propertiesData = $ownerProperties->map(function($property) {
                $thumbnailImage = $property->images->where('IsThumbnail', 1)->first();
                $firstImage = $property->images->first();
                
                // Handle image URL
                $imageUrl = null;
                if ($thumbnailImage) {
                    $imageUrl = $thumbnailImage->ImageURL ?: ($thumbnailImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($thumbnailImage->ImagePath) : null);
                } elseif ($firstImage) {
                    $imageUrl = $firstImage->ImageURL ?: ($firstImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($firstImage->ImagePath) : null);
                }

                return [
                    'PropertyID' => $property->PropertyID,
                    'Title' => $property->Title,
                    'TypePro' => $property->TypePro,
                    'Price' => $property->Price,
                    'Address' => $property->Address,
                    'Ward' => $property->Ward,
                    'District' => $property->District,
                    'Province' => $property->Province,
                    'Status' => $property->Status,
                    'imageUrl' => $imageUrl,
                    'danhMuc' => $property->danhMuc ? [
                        'ten_pro' => $property->danhMuc->ten_pro
                    ] : null,
                    'chiTiet' => $property->chiTiet ? [
                        'Area' => $property->chiTiet->Area ?? null,
                        'Bedroom' => $property->chiTiet->Bedroom ?? 0,
                        'Bath_WC' => $property->chiTiet->Bath_WC ?? 0
                    ] : null
                ];
            });

            return response()->json($propertiesData);
        } catch (\Exception $e) {
            Log::error('Error in getPropertiesForListing: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi tải dữ liệu'], 500);
        }
    }

    public function getNotifications(Request $request)
    {
        $ownerId = Auth::user()->UserID;
        $notifications = [];

        // Lấy các lịch hẹn với trạng thái cần hiển thị thông báo
        $recentAppointments = Appointment::with(['property', 'agentUser', 'cusUser'])
            ->where('OwnerID', $ownerId)
            ->whereIn('Status', ['Khởi Tạo', 'Đang Thực Hiện', 'Hủy Hẹn', 'Hoàn Thành']) // tùy bạn cần
            ->whereDate('AppointmentDateStart', '>=', Carbon::now()->subDays(30))
            ->orderBy('AppointmentDateStart', 'desc')
            ->limit(20)
            ->get();

        foreach ($recentAppointments as $appointment) {
            $appointmentTime = Carbon::parse($appointment->AppointmentDateStart);
            $statusMsg = '';
            $title = 'Cập nhật lịch hẹn';

            // CHÚ Ý: Phải kiểm tra null tránh lỗi
            $agentName = $appointment->agentUser->Name ?? 'Người môi giới';
            $propertyTitle = $appointment->property->Title ?? '(BĐS)';
            $customerName = $appointment->cusUser->Name ?? 'Khách hàng';

            switch ($appointment->Status) {
                case 'Khởi Tạo':
                    $statusMsg = "Người môi giới {$agentName} đã tạo lịch hẹn cho BĐS: {$propertyTitle}";
                    $title = 'Lịch hẹn mới';
                    break;
                case 'Đang Thực Hiện':
                    $statusMsg = "Lịch hẹn cho BĐS: {$propertyTitle} đang được thực hiện.";
                    break;
                case 'Hủy Hẹn':
                    $statusMsg = "Lịch hẹn cho BĐS: {$propertyTitle} đã bị hủy.";
                    $title = 'Lịch hẹn bị hủy';
                    break;
                case 'Hoàn Thành':
                    $statusMsg = "Lịch hẹn cho BĐS: {$propertyTitle} đã hoàn thành.";
                    $title = 'Lịch hẹn hoàn thành';
                    break;
                default:
                    $statusMsg = "Cập nhật mới cho lịch hẹn BĐS: {$propertyTitle}";
            }

            $notifications[] = [
                'id' => 'appointment_' . $appointment->AppointmentID,
                'type' => 'appointment',
                'title' => $title,
                'message' => $statusMsg,
                'time' => $appointmentTime->diffForHumans(),
                'url' => route('owner.appointments.index'),
                'is_read' => false,
                'data' => [
                    'appointment_id' => $appointment->AppointmentID,
                    'property_title' => $propertyTitle,
                    'agent_name' => $agentName,
                    'customer_name' => $customerName,
                    'raw_time' => $appointmentTime->timestamp,
                ]
            ];
        }

        // Bạn có thể bổ sung thêm phần giao dịch tương tự như trên nếu muốn.

        // Sắp xếp tất cả thông báo mới nhất trước
        usort($notifications, function($a, $b) {
            $timeA = $a['data']['raw_time'] ?? 0;
            $timeB = $b['data']['raw_time'] ?? 0;
            return $timeB - $timeA;
        });

        // Giới hạn số lượng thông báo trả về (nếu cần)
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
    

    public function getAppointmentsByStatus(Request $request, $status)
    {
        $ownerId = Auth::user()->UserID;

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

        $appointments = Appointment::with(['property.images', 'agentUser', 'cusUser'])
        ->where('OwnerID', $ownerId)
        ->where('Status', $dbStatus)
        ->orderBy('AppointmentDateStart', 'desc')
        ->get();

        // Format appointments for AJAX response
        $formattedAppointments = $appointments->map(function ($appointment) {
            $propertyImage = null;
            if ($appointment->property && $appointment->property->images->first()) {
                $propertyImage = 'data:image/jpeg;base64,' . base64_encode($appointment->property->images->first()->ImagePath);
            }

            return [
                'id' => $appointment->AppointmentID,
                'property_title' => $appointment->property ? $appointment->property->Title : $appointment->TitleAppoint,
                'property_address' => $appointment->property ? $appointment->property->Address : 'N/A',
                'property_image' => $propertyImage,
                'agent_name' => $appointment->agentUser ? $appointment->agentUser->Name : 'Chưa phân công',
                'agent_phone' => $appointment->agentUser ? $appointment->agentUser->Phone : '',
                'agent_initials' => $appointment->agentUser ? strtoupper(substr($appointment->agentUser->Name, 0, 2)) : 'N/A',
                'customer_name' => $appointment->cusUser ? $appointment->cusUser->Name : 'Chưa có thông tin',
                'customer_phone' => $appointment->cusUser ? $appointment->cusUser->Phone : '',
                'customer_initials' => $appointment->cusUser ? strtoupper(substr($appointment->cusUser->Name, 0, 2)) : 'N/A',
                'title' => $appointment->TitleAppoint ?: 'Không có tiêu đề',
                'description' => $appointment->DescAppoint ?: 'Không có mô tả',
                'date' => $appointment->AppointmentDateStart ? Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y') : 'N/A',
                'end_date' => $appointment->AppointmentDateEnd ? Carbon::parse($appointment->AppointmentDateEnd)->format('d/m/Y') : 'N/A',
                'start_time' => $appointment->AppointmentDateStart ? Carbon::parse($appointment->AppointmentDateStart)->format('H:i') : 'N/A',
                'end_time' => $appointment->AppointmentDateEnd ? Carbon::parse($appointment->AppointmentDateEnd)->format('H:i') : 'N/A',
                'status' => $appointment->Status,
                'status_badge' => $this->getStatusBadge($appointment->Status),

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


    /**
     * Check if status transition is valid
     */
    private function isValidStatusTransition($currentStatus, $newStatus)
    {
        $validTransitions = [
            'Khởi Tạo' => ['Đang Thực Hiện', 'Hủy Hẹn'],
            'Đang Thực Hiện' => ['Hoàn Thành', 'Hủy Hẹn'],
            'Hoàn Thành' => [], // Cannot change from completed
            'Hủy Hẹn' => [] // Cannot change from cancelled
        ];

        return isset($validTransitions[$currentStatus]) && 
               in_array($newStatus, $validTransitions[$currentStatus]);
    }

    /**
     * Confirm appointment (Owner confirms agent's appointment)
     */
    public function confirmAppointment($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            // Check if this owner can confirm this appointment
            if ($appointment->OwnerID !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xác nhận lịch hẹn này'
                ], 403);
            }

            // Check if appointment can be confirmed
            if ($appointment->Status !== 'Khởi Tạo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này không thể xác nhận'
                ], 400);
            }

            $oldStatus = $appointment->Status;
            $newStatus = 'Đang Thực Hiện';

            // Update appointment status
            $appointment->Status = $newStatus;
            $appointment->save();

            // Send notification to agent
            if ($appointment->agentUser) {
                $ownerName = Auth::user()->Name;
                $appointment->agentUser->notify(
                    new \App\Notifications\AppointmentStatusChanged(
                        $appointment, 
                        $oldStatus, 
                        $newStatus,
                        $ownerName
                    )
                );
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn đã được xác nhận thành công',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error confirming appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận lịch hẹn'
            ], 500);
        }
    }

    /**
     * Cancel appointment (Owner cancels agent's appointment)
     */
    public function cancelAppointment($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            // Check if this owner can cancel this appointment
            if ($appointment->OwnerID !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền hủy lịch hẹn này'
                ], 403);
            }

            // Check if appointment can be cancelled
            if (!in_array($appointment->Status, ['Khởi Tạo', 'Đang Thực Hiện'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này không thể hủy'
                ], 400);
            }

            $oldStatus = $appointment->Status;
            $newStatus = 'Hủy Hẹn';

            // Update appointment status
            $appointment->Status = $newStatus;
            $appointment->save();

            // Send notification to agent
            if ($appointment->agentUser) {
                $ownerName = Auth::user()->Name;
                $appointment->agentUser->notify(
                    new \App\Notifications\AppointmentStatusChanged(
                        $appointment, 
                        $oldStatus, 
                        $newStatus,
                        $ownerName
                    )
                );
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn đã được hủy thành công',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy lịch hẹn'
            ], 500);
        }
    }

    /**
     * Finish appointment (Mark as completed)
     */
    public function finishAppointment($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            // Check if this owner can finish this appointment
            if ($appointment->OwnerID !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền hoàn thành lịch hẹn này'
                ], 403);
            }

            // Check if appointment can be finished
            if ($appointment->Status !== 'Đang Thực Hiện') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hoàn thành lịch hẹn đang thực hiện'
                ], 400);
            }

            $oldStatus = $appointment->Status;
            $newStatus = 'Hoàn Thành';

            // Update appointment status
            $appointment->Status = $newStatus;
            $appointment->save();

            // Send notification to agent
            if ($appointment->agentUser) {
                $ownerName = Auth::user()->Name;
                $appointment->agentUser->notify(
                    new \App\Notifications\AppointmentStatusChanged(
                        $appointment, 
                        $oldStatus, 
                        $newStatus,
                        $ownerName
                    )
                );
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn đã được hoàn thành',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error finishing appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hoàn thành lịch hẹn'
            ], 500);
        }
    }

}
