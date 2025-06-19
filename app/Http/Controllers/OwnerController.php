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
use App\Models\Commission;
use App\Models\RevenueReported;
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

        // Statistics for the dashboard
        $stats = [
            'active_properties' => Property::where('OwnerID', $owner->UserID)
                ->where('Status', 'active')
                ->count(),

            'pending_properties' => Property::where('OwnerID', $owner->UserID)
                ->where('Status', 'pending')
                ->count(),

            'completed_transactions' => Transaction::where('OwnerID', $owner->UserID)
                ->where('TranStatus', 'Paid')
                ->count(),

            'pending_appointments' => Appointment::where('OwnerID', $owner->UserID)
                ->whereIn('Status', ['Khởi tạo', 'Đang Thực hiện'])
                ->count(),

            'total_revenue' => Transaction::where('OwnerID', $owner->UserID)
                ->where('TranStatus', 'Paid')
                ->sum('TotalPrice')
        ];

        // Get recent active properties with images and details
        $recentProperties = Property::where('OwnerID', $owner->UserID)
            ->with(['images' => function($query) {
                $query->orderBy('ImageID', 'asc')->limit(1); // Get first image as thumbnail
            }, 'danhMuc', 'chiTiet'])
            ->whereIn('Status', ['active', 'pending'])
            ->orderBy('PostedDate', 'desc')
            ->limit(6)
            ->get();

        // Get upcoming appointments with property and customer info
        $upcomingAppointments = Appointment::where('OwnerID', $owner->UserID)
            ->with(['property', 'cusUser', 'agentUser'])
            ->whereIn('Status', ['Khởi tạo', 'Đang Thực hiện'])
            ->where('AppointmentDateStart', '>=', now())
            ->orderBy('AppointmentDateStart', 'asc')
            ->limit(3)
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::where('OwnerID', $owner->UserID)
            ->with(['property', 'trans_cus', 'trans_agent'])
            ->orderBy('TransactionDate', 'desc')
            ->limit(5)
            ->get();

        return view('owners.dashboard', compact(
            'owner',
            'stats',
            'recentProperties',
            'upcomingAppointments',
            'recentTransactions'
        ));
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
            ->where('Status', 'Đang Thực hiện')
            ->where('AppointmentDateEnd', '<=', now())
            ->update(['Status' => 'Hoàn Thành']);

        $appointments = Appointment::with(['cusUser', 'agentUser', 'property'])
            ->where('OwnerID', $owner->UserID)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        $upcomingAppointments = $appointments->filter(function ($a) {
            return in_array($a->Status, ['Khởi tạo', 'Đang Thực hiện'])
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
            'status' => 'required|string|in:Đang Thực hiện,Hủy Hẹn'
        ]);

        if ($appointment->OwnerID !== Auth::id() || $appointment->Status !== 'Khởi tạo') {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật lịch hẹn này');
        }

        $appointment->Status = $newStatus;
        $appointment->save();

        // Note: Notifications are handled manually, not through Laravel notification system

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
        $search = $request->input('search');

        // Base query for transactions with all related data
        $query = DB::table('transactions')
            ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
            ->leftJoin('user as customer', 'transactions.CusID', '=', 'customer.UserID')
            ->leftJoin('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
            ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
            ->leftJoin('detail_transaction', 'transactions.TransactionID', '=', 'detail_transaction.TransactionID')
            ->select(
                'transactions.*',
                'commission.CommissionID',
                'commission.Amount as commission_amount',
                'commission.Percentage as commission_percentage',
                'commission.StatusCommission',
                'commission.PaidDate',
                'customer.Name as customer_name',
                'customer.Phone as customer_phone',
                'agent.Name as agent_name',
                'agent.Phone as agent_phone',
                'properties.Title as property_title',
                'properties.Address as property_address',
                'detail_transaction.RentMonth as rent_months',
                DB::raw('CASE
                    WHEN commission.StatusCommission IS NULL THEN "no-commission"
                    WHEN commission.StatusCommission = "Success" THEN "Success"
                    WHEN commission.StatusCommission = "Pending" THEN "Pending"
                    ELSE "no-commission"
                END as commission_status')
            )
            ->where('transactions.OwnerID', $ownerId);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('transactions.TransactionID', 'LIKE', "%{$search}%")
                  ->orWhere('customer.Name', 'LIKE', "%{$search}%")
                  ->orWhere('agent.Name', 'LIKE', "%{$search}%")
                  ->orWhere('properties.Title', 'LIKE', "%{$search}%");
            });
        }

        // Apply date filtering
        if ($fromDate && $toDate) {
            $query->whereBetween('transactions.TransactionDate', [$fromDate, $toDate]);
        }

        // Get paginated transactions
        $transactions = $query->orderBy('transactions.TransactionDate', 'desc')->paginate(15);

        // Add commission relationship for easier access in view
        foreach ($transactions as $transaction) {
            if ($transaction->CommissionID) {
                $transaction->commission = (object)[
                    'CommissionID' => $transaction->CommissionID,
                    'Amount' => $transaction->commission_amount,
                    'Percentage' => $transaction->commission_percentage,
                    'StatusCommission' => $transaction->StatusCommission,
                    'PaidDate' => $transaction->PaidDate,
                    'TypeCom' => $transaction->TransactionType,
                    'TransactionID' => $transaction->TransactionID,
                    'agent_name' => $transaction->agent_name,
                    'agent_phone' => $transaction->agent_phone
                ];
            } else {
                $transaction->commission = null;
            }
        }

        // Calculate statistics
        $allTransactions = DB::table('transactions')
            ->where('OwnerID', $ownerId)
            ->whereBetween('TransactionDate', [$fromDate, $toDate])
            ->get();

        $totalTransactionValue = $allTransactions->sum('TotalPrice');
        $transactionCount = $allTransactions->count();
        $successRate = $transactionCount > 0 ?
            ($allTransactions->where('TranStatus', 'Paid')->count() / $transactionCount) * 100 : 0;

        // Commission statistics
        $commissionStats = DB::table('commission')
            ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
            ->where('transactions.OwnerID', $ownerId)
            ->whereBetween('transactions.TransactionDate', [$fromDate, $toDate])
            ->selectRaw('
                SUM(CASE WHEN commission.StatusCommission = "Success" THEN commission.Amount ELSE 0 END) as paid_commissions,
                SUM(CASE WHEN commission.StatusCommission = "Pending" THEN commission.Amount ELSE 0 END) as pending_commissions,
                COUNT(CASE WHEN commission.StatusCommission = "Success" THEN 1 END) as paid_count,
                COUNT(CASE WHEN commission.StatusCommission = "Pending" THEN 1 END) as pending_count
            ')
            ->first();

        $paidCommissions = $commissionStats->paid_commissions ?? 0;
        $pendingCommissions = $commissionStats->pending_commissions ?? 0;
        $paidCommissionCount = $commissionStats->paid_count ?? 0;
        $pendingCommissionCount = $commissionStats->pending_count ?? 0;

        return view('owners.transactions.index', compact(
            'transactions',
            'fromDate',
            'toDate',
            'totalTransactionValue',
            'paidCommissions',
            'pendingCommissions',
            'paidCommissionCount',
            'pendingCommissionCount',
            'successRate'
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
        if (Auth::user()->Role == 'Owner' && Auth::user()->UserID != $transaction->OwnerID) {
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
        if (Auth::user()->Role == 'Owner' && Auth::user()->UserID != $transaction->OwnerID) {
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
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'Name' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|email|max:255|unique:user,Email,'.$user->UserID.',UserID',
            'Address' => 'nullable|string|max:255',
            'Avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Xử lý upload avatar nếu có
        if ($request->hasFile('Avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->Avatar && file_exists(public_path('storage/avatars/' . $user->Avatar))) {
                unlink(public_path('storage/avatars/' . $user->Avatar));
            }

            $avatar = $request->file('Avatar');
            // Generate unique filename - chỉ sử dụng số để tránh lỗi với tên tiếng Việt
            $extension = $avatar->getClientOriginalExtension();
            $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

            // Ensure avatars directory exists
            $avatarPath = public_path('storage/avatars');
            if (!file_exists($avatarPath)) {
                mkdir($avatarPath, 0755, true);
            }

            $avatar->move($avatarPath, $filename);
            $user->Avatar = $filename;
        }

        $user->Name = $validated['Name'];
        $user->Phone = $validated['Phone'];
        $user->Email = $validated['Email'];
        $user->Address = $validated['Address'] ?? $user->Address;

        $saved = $user->save();

        if (!$saved) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật thông tin']);
        }

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
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Kiểm tra mật khẩu hiện tại (sử dụng MD5)
        if (md5($validated['current_password']) !== $user->PasswordHash) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        // Lưu mật khẩu mới (sử dụng MD5)
        $user->PasswordHash = md5($validated['password']);

        $saved = $user->save();

        if (!$saved) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi đổi mật khẩu']);
        }

        return redirect()->route('owner.profile')->with('success', 'Đổi mật khẩu thành công!');
    }

    public function storePropertyListing(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,PropertyID',
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

                // Handle image URL using ImageHelper
                $imageUrl = null;
                if ($thumbnailImage) {
                    $imageUrl = \App\Helpers\ImageHelper::getImageUrl($thumbnailImage->ImagePath);
                } elseif ($firstImage) {
                    $imageUrl = \App\Helpers\ImageHelper::getImageUrl($firstImage->ImagePath);
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
            ->whereIn('Status', ['Khởi tạo', 'Đang Thực hiện', 'Hủy Hẹn', 'Hoàn Thành']) // tùy bạn cần
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
                case 'Khởi tạo':
                    $statusMsg = "Người môi giới {$agentName} đã tạo lịch hẹn cho BĐS: {$propertyTitle}";
                    $title = 'Lịch hẹn mới';
                    break;
                case 'Đang Thực hiện':
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
            'khoitao' => 'Khởi tạo',
            'dangthuchien' => 'Đang Thực hiện',
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
            case 'Khởi tạo':
                return '<span class="badge bg-warning text-dark">Khởi Tạo</span>';
            case 'Đang Thực hiện':
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
            'Khởi tạo' => ['Đang Thực hiện', 'Hủy Hẹn'],
            'Đang Thực hiện' => ['Hoàn Thành', 'Hủy Hẹn'],
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
            if ($appointment->Status !== 'Khởi tạo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này không thể xác nhận'
                ], 400);
            }

            $oldStatus = $appointment->Status;
            $newStatus = 'Đang Thực hiện';

            // Update appointment status
            $appointment->Status = $newStatus;
            $appointment->save();

            // Note: Notifications are handled manually, not through Laravel notification system

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
            if (!in_array($appointment->Status, ['Khởi tạo', 'Đang Thực hiện'])) {
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

            // Note: Notifications are handled manually, not through Laravel notification system

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

    /**
     * Lấy chi tiết lịch hẹn
     */
    public function getAppointmentDetail($id)
    {
        try {
            $appointment = Appointment::with([
                'property' => function($query) {
                    $query->select('PropertyID', 'Title', 'Address', 'Ward', 'District', 'Price', 'OwnerID');
                },
                'property.owner' => function($query) {
                    $query->select('UserID', 'Name', 'Phone', 'IdentityCard', 'Address');
                },
                'cusUser' => function($query) {
                    $query->select('UserID', 'Name', 'Phone', 'Address', 'Ward', 'District', 'Province');
                },
                'agentUser' => function($query) {
                    $query->select('UserID', 'Name', 'Phone', 'Address', 'Ward', 'District', 'Province');
                }
            ])->findOrFail($id);

            // Check if this owner can view this appointment
            if ($appointment->OwnerID !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem chi tiết lịch hẹn này'
                ], 403);
            }

            $formattedAppointment = [
                'id' => $appointment->AppointmentID,
                'title' => $appointment->TitleAppoint ?: 'Không có tiêu đề',
                'description' => $appointment->DescAppoint ?: 'Không có mô tả',
                'status' => $appointment->Status,
                'status_badge' => $this->getStatusBadge($appointment->Status),
                'date' => $appointment->AppointmentDateStart ? Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y') : 'N/A',
                'start_time' => $appointment->AppointmentDateStart ? Carbon::parse($appointment->AppointmentDateStart)->format('H:i') : 'N/A',
                'end_time' => $appointment->AppointmentDateEnd ? Carbon::parse($appointment->AppointmentDateEnd)->format('H:i') : 'N/A',

                // Property information
                'property' => [
                    'id' => $appointment->property ? $appointment->property->PropertyID : null,
                    'title' => $appointment->property ? $appointment->property->Title : 'N/A',
                    'address' => $appointment->property ? $appointment->property->Address : 'N/A',
                    'ward' => $appointment->property ? $appointment->property->Ward : 'N/A',
                    'district' => $appointment->property ? $appointment->property->District : 'N/A',
                    'price' => $appointment->property ? number_format($appointment->property->Price, 0, ',', '.') . ' VNĐ' : 'N/A',
                    'full_address' => $appointment->property ?
                        ($appointment->property->Address .
                        ($appointment->property->Ward ? ', ' . $appointment->property->Ward : '') .
                        ($appointment->property->District ? ', ' . $appointment->property->District : '')) : 'N/A'
                ],

                // Property owner information
                'property_owner' => [
                    'name' => $appointment->property && $appointment->property->owner ? $appointment->property->owner->Name : 'N/A',
                    'phone' => $appointment->property && $appointment->property->owner ? $appointment->property->owner->Phone : 'N/A',
                    'cmnd' => $appointment->property && $appointment->property->owner ? $appointment->property->owner->IdentityCard : 'N/A',
                    'address' => $appointment->property && $appointment->property->owner ? $appointment->property->owner->Address : 'N/A'
                ],

                // Customer information
                'customer' => [
                    'name' => $appointment->cusUser ? $appointment->cusUser->Name : 'N/A',
                    'phone' => $appointment->cusUser ? $appointment->cusUser->Phone : 'N/A',
                    'address' => $appointment->cusUser ? $this->getFullAddress(
                        $appointment->cusUser->Address,
                        $appointment->cusUser->Ward,
                        $appointment->cusUser->District,
                        $appointment->cusUser->Province
                    ) : 'N/A',
                    'initials' => $appointment->cusUser ? strtoupper(substr($appointment->cusUser->Name, 0, 2)) : 'N/A'
                ],

                // Agent information
                'agent' => [
                    'name' => $appointment->agentUser ? $appointment->agentUser->Name : 'Chưa phân công',
                    'phone' => $appointment->agentUser ? $appointment->agentUser->Phone : 'N/A',
                    'address' => $appointment->agentUser ? $this->getFullAddress(
                        $appointment->agentUser->Address,
                        $appointment->agentUser->Ward,
                        $appointment->agentUser->District,
                        $appointment->agentUser->Province
                    ) : 'N/A',
                    'initials' => $appointment->agentUser ? strtoupper(substr($appointment->agentUser->Name, 0, 2)) : 'N/A'
                ]
            ];

            return response()->json([
                'success' => true,
                'appointment' => $formattedAppointment
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting appointment detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin lịch hẹn'
            ], 500);
        }
    }

    /**
     * Helper method to create full address from components
     */
    private function getFullAddress($address, $ward, $district, $province)
    {
        $parts = array_filter([$address, $ward, $district, $province]);
        return !empty($parts) ? implode(', ', $parts) : 'N/A';
    }

    /**
     * Hiển thị lịch sử giao dịch
     */
    public function transactionHistory(Request $request)
    {
        $user = Auth::user();

        // Validate user role
        if ($user->Role != 'Owner') {
            abort(403, 'Unauthorized action.');
        }

        $ownerId = $user->UserID;

        // Process date filter
        $fromDate = $request->input('from_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        // Base query for transactions using the correct table name (transactions)
        $transactions = DB::table('transactions')
            ->join('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
            ->join('user as owner', 'transactions.OwnerID', '=', 'owner.UserID')
            ->join('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
            ->join('user as customer', 'transactions.CusID', '=', 'customer.UserID')
            ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
            ->select(
                'transactions.*',
                'properties.Title as PropertyTitle',
                'properties.Address as PropertyAddress',
                'properties.TypePro',
                'agent.Name as AgentName',
                'customer.Name as CustomerName',
                'commission.Amount as CommissionAmount',
                'commission.Percentage as CommissionPercentage',
                'commission.StatusCommission'
            )
            ->where('transactions.OwnerID', $ownerId)
            ->whereBetween('transactions.TransactionDate', [$fromDate, $toDate])
            ->orderBy('transactions.TransactionDate', 'desc')
            ->get();

        // Monthly statistics
        $monthlyStats = DB::table('transactions')
            ->select(
                DB::raw('YEAR(TransactionDate) as year'),
                DB::raw('MONTH(TransactionDate) as month'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(TotalPrice) as total_value'),
                DB::raw('SUM(CASE WHEN TranStatus = "Paid" THEN TotalPrice ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN TransactionType = "Sale" THEN TotalPrice ELSE 0 END) as sale_value'),
                DB::raw('SUM(CASE WHEN TransactionType = "Rent" THEN TotalPrice ELSE 0 END) as rental_value')
            )
            ->where('OwnerID', $ownerId)
            ->whereBetween('TransactionDate', [$fromDate, $toDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Commission summary
        $commissionSummary = DB::table('commission')
            ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
            ->join('user', 'commission.AgentID', '=', 'user.UserID')
            ->select(
                'commission.AgentID',
                'user.Name as AgentName',
                DB::raw('SUM(commission.Amount) as TotalCommission'),
                DB::raw('SUM(CASE WHEN commission.TypeCom = "Rent" THEN commission.Amount ELSE 0 END) as RentCommission'),
                DB::raw('SUM(CASE WHEN commission.TypeCom = "Sale" THEN commission.Amount ELSE 0 END) as SaleCommission'),
                DB::raw('SUM(CASE WHEN commission.StatusCommission = "Success" THEN commission.Amount ELSE 0 END) as PaidCommission'),
                DB::raw('SUM(CASE WHEN commission.StatusCommission = "Pending" THEN commission.Amount ELSE 0 END) as PendingCommission')
            )
            ->where('transactions.OwnerID', $ownerId)
            ->whereBetween('transactions.TransactionDate', [$fromDate, $toDate])
            ->groupBy('commission.AgentID', 'user.Name')
            ->get();

        return view('owners.transactions.history', compact(
            'transactions',
            'monthlyStats',
            'commissionSummary',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Quản lý doanh thu
     */
    public function revenueManagement(Request $request)
    {
        $user = Auth::user();

        // Validate user role
        if ($user->Role != 'Owner') {
            abort(403, 'Unauthorized action.');
        }

        $ownerId = $user->UserID;
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));

        // Get monthly revenue data for the selected year
        $monthlyRevenue = DB::table('transactions')
            ->select(
                DB::raw('MONTH(TransactionDate) as month'),
                DB::raw('SUM(TotalPrice) as total_revenue'),
                DB::raw('SUM(CASE WHEN TransactionType = "Sale" THEN TotalPrice ELSE 0 END) as sale_revenue'),
                DB::raw('SUM(CASE WHEN TransactionType = "Rent" THEN TotalPrice ELSE 0 END) as rental_revenue'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->where('OwnerID', $ownerId)
            ->whereYear('TransactionDate', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get commission paid to agents for the selected year
        $monthlyCommission = DB::table('commission')
            ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
            ->select(
                DB::raw('MONTH(transactions.TransactionDate) as month'),
                DB::raw('SUM(commission.Amount) as total_commission'),
                DB::raw('SUM(CASE WHEN commission.TypeCom = "Sale" THEN commission.Amount ELSE 0 END) as sale_commission'),
                DB::raw('SUM(CASE WHEN commission.TypeCom = "Rent" THEN commission.Amount ELSE 0 END) as rent_commission')
            )
            ->where('transactions.OwnerID', $ownerId)
            ->whereYear('transactions.TransactionDate', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Detail for specific month if selected
        $monthDetail = null;
        if ($month) {
            $monthDetail = DB::table('transactions')
                ->join('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->join('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
                ->join('user as customer', 'transactions.CusID', '=', 'customer.UserID')
                ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
                ->select(
                    'transactions.TransactionID',
                    'transactions.TransactionDate',
                    'transactions.TotalPrice',
                    'transactions.TransactionType',
                    'transactions.TranStatus',
                    'properties.Title as PropertyTitle',
                    'agent.Name as AgentName',
                    'customer.Name as CustomerName',
                    'commission.Amount as CommissionAmount',
                    'commission.StatusCommission'
                )
                ->where('transactions.OwnerID', $ownerId)
                ->whereYear('transactions.TransactionDate', $year)
                ->whereMonth('transactions.TransactionDate', $month)
                ->orderBy('transactions.TransactionDate', 'desc')
                ->get();
        }

        // Total annual statistics
        $annualStats = DB::table('transactions')
            ->select(
                DB::raw('SUM(TotalPrice) as annual_revenue'),
                DB::raw('SUM(CASE WHEN TransactionType = "Sale" THEN TotalPrice ELSE 0 END) as annual_sale_revenue'),
                DB::raw('SUM(CASE WHEN TransactionType = "Rent" THEN TotalPrice ELSE 0 END) as annual_rental_revenue'),
                DB::raw('COUNT(*) as annual_transaction_count')
            )
            ->where('OwnerID', $ownerId)
            ->whereYear('TransactionDate', $year)
            ->first();

        // Total annual commission
        $annualCommission = DB::table('commission')
            ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
            ->select(
                DB::raw('SUM(commission.Amount) as annual_commission'),
                DB::raw('SUM(CASE WHEN commission.StatusCommission = "Success" THEN commission.Amount ELSE 0 END) as paid_commission'),
                DB::raw('SUM(CASE WHEN commission.StatusCommission = "Pending" THEN commission.Amount ELSE 0 END) as pending_commission')
            )
            ->where('transactions.OwnerID', $ownerId)
            ->whereYear('transactions.TransactionDate', $year)
            ->first();

        // Years with transactions for dropdown
        $availableYears = DB::table('transactions')
            ->select(DB::raw('DISTINCT YEAR(TransactionDate) as year'))
            ->where('OwnerID', $ownerId)
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return view('owners.transactions.revenue', compact(
            'monthlyRevenue',
            'monthlyCommission',
            'monthDetail',
            'annualStats',
            'annualCommission',
            'availableYears',
            'year',
            'month'
        ));
    }

    /**
     * Quản lý hoa hồng cho môi giới
     */
    public function commissionManagement(Request $request)
    {
        $user = Auth::user();

        // Validate user role
        if ($user->Role != 'Owner') {
            abort(403, 'Unauthorized action.');
        }

        $ownerId = $user->UserID;
        $status = $request->input('status', 'all');

        // Lấy tất cả các giao dịch và thông tin hoa hồng liên quan
        $query = DB::table('commission')
            ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
            ->join('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
            ->join('user', 'commission.AgentID', '=', 'user.UserID')
            ->select(
                'commission.*',
                'transactions.TransactionDate',
                'transactions.TotalPrice',
                'transactions.TransactionType',
                'transactions.TranStatus',
                'properties.Title as PropertyTitle',
                'user.Name as AgentName'
            )
            ->where('transactions.OwnerID', $ownerId);

        // Lọc theo trạng thái hoa hồng nếu có
        if ($status && $status != 'all') {
            $query->where('commission.StatusCommission', $status);
        }

        $commissions = $query->orderBy('transactions.TransactionDate', 'desc')->get();

        // Thống kê tổng quan
        $commissionStats = [
            'total' => $commissions->sum('Amount'),
            'paid' => $commissions->where('StatusCommission', 'Success')->sum('Amount'),
            'pending' => $commissions->where('StatusCommission', 'Pending')->sum('Amount'),
            'cancelled' => $commissions->where('StatusCommission', 'Cancelled')->sum('Amount'),
            'count' => $commissions->count()
        ];

        // Thống kê theo loại giao dịch
        $typeStats = [
            'rent' => [
                'total' => $commissions->where('TypeCom', 'Rent')->sum('Amount'),
                'count' => $commissions->where('TypeCom', 'Rent')->count()
            ],
            'sale' => [
                'total' => $commissions->where('TypeCom', 'Sale')->sum('Amount'),
                'count' => $commissions->where('TypeCom', 'Sale')->count()
            ]
        ];

        // Thống kê theo agent
        $agentStats = [];
        foreach ($commissions as $commission) {
            $agentId = $commission->AgentID;
            if (!isset($agentStats[$agentId])) {
                $agentStats[$agentId] = [
                    'name' => $commission->AgentName,
                    'total' => 0,
                    'paid' => 0,
                    'pending' => 0,
                    'count' => 0
                ];
            }

            $agentStats[$agentId]['total'] += $commission->Amount;
            if ($commission->StatusCommission == 'Success') {
                $agentStats[$agentId]['paid'] += $commission->Amount;
            } else if ($commission->StatusCommission == 'Pending') {
                $agentStats[$agentId]['pending'] += $commission->Amount;
            }
            $agentStats[$agentId]['count']++;
        }

        return view('owners.transactions.commissions', compact(
            'commissions',
            'commissionStats',
            'typeStats',
            'agentStats',
            'status'
        ));
    }

    /**
     * Hiển thị trang tổng quan về giao dịch và hoa hồng
     */
    public function transactionsOverview()
    {
        $user = Auth::user();

        // Validate user role
        if ($user->Role != 'Owner') {
            abort(403, 'Unauthorized action.');
        }

        return view('owners.transactions.overview');
    }

    /**
     * Cập nhật trạng thái hoa hồng
     */
    public function updateCommissionStatus(Request $request, $id)
    {
        try {
            $commission = Commission::findOrFail($id);
            $transaction = Transaction::find($commission->TransactionID);

            // Kiểm tra nếu người dùng hiện tại là chủ sở hữu của giao dịch này
            if ($transaction && $transaction->OwnerID != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật hoa hồng này'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:Success,Pending,Cancelled'
            ]);

            $commission->StatusCommission = $request->status;

            // Nếu đã thanh toán, cập nhật ngày thanh toán
            if ($request->status == 'Success') {
                $commission->PaidDate = Carbon::now()->format('Y-m-d');
            } elseif ($request->status == 'Pending') {
                $commission->PaidDate = null;
            }

            $commission->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái hoa hồng thành công',
                'new_status' => $request->status,
                'paid_date' => $commission->PaidDate ? Carbon::parse($commission->PaidDate)->format('d/m/Y') : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating commission status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái hoa hồng'
            ], 500);
        }
    }

    /**
     * Tạo hoa hồng cho giao dịch
     */
    public function createCommission(Request $request)
    {
        try {
            $validated = $request->validate([
                'transaction_id' => 'required|exists:transactions,TransactionID',
                'transaction_type' => 'required|in:Sale,Rent',
                'total_price' => 'required|numeric|min:0',
                'rent_months' => 'nullable|integer|min:1',
                'percentage' => 'required|numeric|min:1|max:200',
                'amount' => 'required|numeric|min:0'
            ]);

            // Kiểm tra transaction thuộc về owner hiện tại
            $transaction = DB::table('transactions')
                ->where('TransactionID', $validated['transaction_id'])
                ->where('OwnerID', Auth::user()->UserID)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không tồn tại hoặc bạn không có quyền'
                ], 403);
            }

            // Kiểm tra xem đã có commission chưa
            $existingCommission = DB::table('commission')
                ->where('TransactionID', $validated['transaction_id'])
                ->first();

            if ($existingCommission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch này đã có hoa hồng'
                ], 400);
            }

            // Validate percentage range based on transaction type
            if ($validated['transaction_type'] === 'Sale') {
                if ($validated['percentage'] < 1 || $validated['percentage'] > 3) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tỷ lệ hoa hồng cho bán phải từ 1% đến 3%'
                    ], 400);
                }
            } else { // Rent
                if ($validated['percentage'] < 50 || $validated['percentage'] > 200) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tỷ lệ hoa hồng cho thuê phải từ 50% đến 200% của 1 tháng'
                    ], 400);
                }
            }

            // Tạo commission mới
            $commissionId = DB::table('commission')->insertGetId([
                'TransactionID' => $validated['transaction_id'],
                'AgentID' => $transaction->AgentID,
                'Amount' => $validated['amount'],
                'Percentage' => $validated['percentage'] / 100, // Convert to decimal
                'TypeCom' => $validated['transaction_type'],
                'StatusCommission' => 'Pending',
                'PaidDate' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo hoa hồng thành công',
                'commission_id' => $commissionId
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating commission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo hoa hồng'
            ], 500);
        }
    }

    /**
     * Thanh toán hoa hồng
     */
    public function payCommission(Request $request)
    {
        try {
            $validated = $request->validate([
                'commission_id' => 'required|exists:commission,CommissionID',
                'payment_method' => 'required|in:transfer,cash,vnpay',
                'note' => 'nullable|string|max:500'
            ]);

            // Kiểm tra commission thuộc về transaction của owner hiện tại
            $commission = DB::table('commission')
                ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
                ->where('commission.CommissionID', $validated['commission_id'])
                ->where('transactions.OwnerID', Auth::user()->UserID)
                ->first();

            if (!$commission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hoa hồng không tồn tại hoặc bạn không có quyền'
                ], 403);
            }

            if ($commission->StatusCommission === 'Success') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hoa hồng này đã được thanh toán'
                ], 400);
            }

            // If VNPAY, redirect to VNPAY processing
            if ($validated['payment_method'] === 'vnpay') {
                return $this->processVNPayCommission($request);
            }

            // Cập nhật trạng thái commission cho transfer và cash
            DB::table('commission')
                ->where('CommissionID', $validated['commission_id'])
                ->update([
                    'StatusCommission' => 'Success',
                    'PaidDate' => Carbon::now()->format('Y-m-d')
                ]);

            // Log payment (có thể thêm bảng payment_logs nếu cần)
            Log::info('Commission payment completed', [
                'commission_id' => $validated['commission_id'],
                'owner_id' => Auth::user()->UserID,
                'payment_method' => $validated['payment_method'],
                'note' => $validated['note'],
                'amount' => $commission->Amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán hoa hồng thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error paying commission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thanh toán hoa hồng'
            ], 500);
        }
    }

    /**
     * Process VNPAY commission payment
     */
    public function processVNPayCommission(Request $request)
    {
        try {
            $request->validate([
                'commission_id' => 'required|exists:commission,CommissionID',
                'payment_method' => 'required|in:vnpay',
                'note' => 'nullable|string|max:500'
            ]);

            $owner = Auth::user();
            $commissionId = $request->commission_id;

            // Get commission and transaction details
            $commission = DB::table('commission')
                ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
                ->where('commission.CommissionID', $commissionId)
                ->where('transactions.OwnerID', $owner->UserID)
                ->select('commission.*', 'transactions.TransactionID')
                ->first();

            if (!$commission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hoa hồng hoặc bạn không có quyền truy cập'
                ], 404);
            }

            if ($commission->StatusCommission === 'Success') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hoa hồng này đã được thanh toán'
                ], 400);
            }

            // Create VNPAY payment URL using PaymentController
            $paymentController = new \App\Http\Controllers\PaymentController();
            $paymentRequest = new Request([
                'transaction_id' => $commission->TransactionID,
                'payment_type' => 'commission',
                'amount' => $commission->Amount,
                'payment_description' => $request->note ?? "Thanh toán hoa hồng #{$commissionId}"
            ]);

            $response = $paymentController->processTransactionPayment($paymentRequest);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);

                if ($data['success']) {
                    return response()->json([
                        'success' => true,
                        'payment_url' => $data['payment_url'],
                        'message' => 'Đang chuyển hướng đến VNPAY...'
                    ]);
                }
            }

            // Fallback if PaymentController fails
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo liên kết thanh toán VNPAY'
            ]);

        } catch (\Exception $e) {
            Log::error('VNPAY commission payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý thanh toán VNPAY: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * In hóa đơn hoa hồng
     */
    public function commissionInvoice($id)
    {
        try {
            $commission = DB::table('commission')
                ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
                ->join('user as agent', 'commission.AgentID', '=', 'agent.UserID')
                ->join('user as owner', 'transactions.OwnerID', '=', 'owner.UserID')
                ->join('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->select(
                    'commission.*',
                    'transactions.*',
                    'agent.Name as agent_name',
                    'agent.Phone as agent_phone',
                    'agent.Address as agent_address',
                    'owner.Name as owner_name',
                    'owner.Phone as owner_phone',
                    'owner.Address as owner_address',
                    'properties.Title as property_title',
                    'properties.Address as property_address'
                )
                ->where('commission.CommissionID', $id)
                ->where('transactions.OwnerID', Auth::user()->UserID)
                ->first();

            if (!$commission) {
                abort(404, 'Hóa đơn hoa hồng không tồn tại');
            }

            return view('owners.transactions.commission-invoice', compact('commission'));

        } catch (\Exception $e) {
            Log::error('Error generating commission invoice: ' . $e->getMessage());
            abort(500, 'Có lỗi xảy ra khi tạo hóa đơn');
        }
    }

    /**
     * Xuất báo cáo giao dịch
     */
    public function exportTransactions(Request $request)
    {
        $ownerId = Auth::user()->UserID;
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get transactions data
        $transactions = DB::table('transactions')
            ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
            ->leftJoin('user as customer', 'transactions.CusID', '=', 'customer.UserID')
            ->leftJoin('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
            ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
            ->select(
                'transactions.*',
                'commission.Amount as commission_amount',
                'commission.StatusCommission',
                'customer.Name as customer_name',
                'agent.Name as agent_name',
                'properties.Title as property_title'
            )
            ->where('transactions.OwnerID', $ownerId)
            ->whereBetween('transactions.TransactionDate', [$fromDate, $toDate])
            ->orderBy('transactions.TransactionDate', 'desc')
            ->get();

        $filename = 'transactions_' . $fromDate . '_to_' . $toDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add headers
            fputcsv($file, [
                'Mã giao dịch',
                'Ngày giao dịch',
                'Bất động sản',
                'Loại giao dịch',
                'Giá trị (VND)',
                'Khách hàng',
                'Môi giới',
                'Hoa hồng (VND)',
                'Trạng thái hoa hồng',
                'Trạng thái giao dịch'
            ]);

            // Add data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->TransactionID,
                    Carbon::parse($transaction->TransactionDate)->format('d/m/Y'),
                    $transaction->property_title ?? 'N/A',
                    $transaction->TransactionType === 'Sale' ? 'Bán' : 'Thuê',
                    number_format($transaction->TotalPrice, 0, ',', '.'),
                    $transaction->customer_name ?? 'N/A',
                    $transaction->agent_name ?? 'N/A',
                    $transaction->commission_amount ? number_format($transaction->commission_amount, 0, ',', '.') : '0',
                    $transaction->StatusCommission === 'Success' ? 'Đã trả' :
                        ($transaction->StatusCommission === 'Pending' ? 'Chờ trả' : 'Chưa chia'),
                    $transaction->TranStatus === 'Paid' ? 'Đã thanh toán' :
                        ($transaction->TranStatus === 'Pending' ? 'Chờ thanh toán' : 'Đã hủy')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get detailed transaction information for modal display
     */
    public function transactionDetail($transactionId)
    {
        try {
            $ownerId = Auth::user()->UserID;

            // Get transaction with all related data
            $transaction = DB::table('transactions')
                ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
                ->leftJoin('user as customer', 'transactions.CusID', '=', 'customer.UserID')
                ->leftJoin('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
                ->leftJoin('user as owner', 'transactions.OwnerID', '=', 'owner.UserID')
                ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->leftJoin('danhmuc_pro', 'properties.PropertyType', '=', 'danhmuc_pro.Protype_ID')
                ->select(
                    'transactions.*',
                    // Customer info
                    'customer.UserID as customer_id',
                    'customer.Name as customer_name',
                    'customer.Email as customer_email',
                    'customer.Phone as customer_phone',
                    'customer.IdentityCard as customer_identity',
                    'customer.Birth as customer_birth',
                    'customer.Sex as customer_sex',
                    'customer.Address as customer_address',
                    'customer.Ward as customer_ward',
                    'customer.District as customer_district',
                    'customer.Province as customer_province',
                    // Agent info
                    'agent.UserID as agent_id',
                    'agent.Name as agent_name',
                    'agent.Email as agent_email',
                    'agent.Phone as agent_phone',
                    'agent.IdentityCard as agent_identity',
                    'agent.Birth as agent_birth',
                    'agent.Sex as agent_sex',
                    'agent.Address as agent_address',
                    'agent.Ward as agent_ward',
                    'agent.District as agent_district',
                    'agent.Province as agent_province',
                    // Owner info
                    'owner.UserID as owner_id',
                    'owner.Name as owner_name',
                    'owner.Email as owner_email',
                    'owner.Phone as owner_phone',
                    'owner.IdentityCard as owner_identity',
                    'owner.Birth as owner_birth',
                    'owner.Sex as owner_sex',
                    'owner.Address as owner_address',
                    'owner.Ward as owner_ward',
                    'owner.District as owner_district',
                    'owner.Province as owner_province',
                    // Property info
                    'properties.PropertyID',
                    'properties.Title as property_title',
                    'properties.Address as property_address',
                    'properties.Ward as property_ward',
                    'properties.District as property_district',
                    'properties.Province as property_province',
                    'properties.Price as property_price',
                    'properties.PropertyType',
                    'danhmuc_pro.ten_pro as property_type_name',
                    // Commission info
                    'commission.CommissionID',
                    'commission.Amount as commission_amount',
                    'commission.Percentage as commission_percentage',
                    'commission.StatusCommission',
                    'commission.PaidDate'
                )
                ->where('transactions.TransactionID', $transactionId)
                ->where('transactions.OwnerID', $ownerId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch hoặc bạn không có quyền truy cập'
                ], 404);
            }

            // Get payment details
            $paymentDetails = DB::table('detail_transaction')
                ->where('TransactionID', $transactionId)
                ->orderBy('Num_Pay')
                ->get();

            // Get documents
            $documents = DB::table('documents')
                ->where('TransactionID', $transactionId)
                ->orderBy('UploadedDate', 'desc')
                ->get();

            // Format the response data
            $responseData = [
                'TransactionID' => $transaction->TransactionID,
                'PropertyID' => $transaction->PropertyID,
                'TotalPrice' => $transaction->TotalPrice,
                'TransactionDate' => $transaction->TransactionDate,
                'TransactionType' => $transaction->TransactionType,
                'TranStatus' => $transaction->TranStatus,

                // Customer data
                'customer' => [
                    'UserID' => $transaction->customer_id,
                    'Name' => $transaction->customer_name,
                    'Email' => $transaction->customer_email,
                    'Phone' => $transaction->customer_phone,
                    'IdentityCard' => $transaction->customer_identity,
                    'Birth' => $transaction->customer_birth,
                    'Sex' => $transaction->customer_sex,
                    'Address' => $transaction->customer_address,
                    'Ward' => $transaction->customer_ward,
                    'District' => $transaction->customer_district,
                    'Province' => $transaction->customer_province
                ],

                // Agent data
                'agent' => [
                    'UserID' => $transaction->agent_id,
                    'Name' => $transaction->agent_name,
                    'Email' => $transaction->agent_email,
                    'Phone' => $transaction->agent_phone,
                    'IdentityCard' => $transaction->agent_identity,
                    'Birth' => $transaction->agent_birth,
                    'Sex' => $transaction->agent_sex,
                    'Address' => $transaction->agent_address,
                    'Ward' => $transaction->agent_ward,
                    'District' => $transaction->agent_district,
                    'Province' => $transaction->agent_province
                ],

                // Owner data
                'owner' => [
                    'UserID' => $transaction->owner_id,
                    'Name' => $transaction->owner_name,
                    'Email' => $transaction->owner_email,
                    'Phone' => $transaction->owner_phone,
                    'IdentityCard' => $transaction->owner_identity,
                    'Birth' => $transaction->owner_birth,
                    'Sex' => $transaction->owner_sex,
                    'Address' => $transaction->owner_address,
                    'Ward' => $transaction->owner_ward,
                    'District' => $transaction->owner_district,
                    'Province' => $transaction->owner_province
                ],

                // Property data
                'property' => [
                    'PropertyID' => $transaction->PropertyID,
                    'Title' => $transaction->property_title,
                    'Address' => $transaction->property_address,
                    'Ward' => $transaction->property_ward,
                    'District' => $transaction->property_district,
                    'Province' => $transaction->property_province,
                    'Price' => $transaction->property_price,
                    'PropertyType' => $transaction->PropertyType,
                    'PropertyTypeName' => $transaction->property_type_name
                ],

                // Payment details
                'payment_details' => $paymentDetails,

                // Documents
                'documents' => $documents,

                // Commission data
                'commission' => $transaction->CommissionID ? [
                    'CommissionID' => $transaction->CommissionID,
                    'Amount' => $transaction->commission_amount,
                    'Percentage' => $transaction->commission_percentage,
                    'StatusCommission' => $transaction->StatusCommission,
                    'PaidDate' => $transaction->PaidDate
                ] : null
            ];

            return response()->json([
                'success' => true,
                'transaction' => $responseData
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching transaction detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin giao dịch'
            ], 500);
        }
    }
}
