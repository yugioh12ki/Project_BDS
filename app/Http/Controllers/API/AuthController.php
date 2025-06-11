<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
              $transaction = DB::table('transactions')
                ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->leftJoin('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
                ->leftJoin('user as buyer', 'transactions.BuyerID', '=', 'buyer.UserID')
                ->leftJoin('user as seller', 'transactions.SellerID', '=', 'seller.UserID')
                ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
                ->select(
                    'transactions.*',
                    'properties.Title as PropertyTitle',
                    'properties.Address as PropertyAddress',registration
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:user,Email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'required|string|max:20|unique:user,Phone',
                'role' => 'in:Customer,Owner,Agent,Admin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'UserID' => 'USER_' . uniqid() . '_' . time(),
                'Name' => $request->name,
                'Email' => $request->email,
                'PasswordHash' => md5($request->password), // Use MD5 for compatibility
                'Phone' => $request->phone,
                'Role' => $request->role ?? 'Customer',
                'StatusUser' => 'active',
                'Birth' => now()->subYears(25)->format('Y-m-d'), // Default birth
                'Sex' => 'Khác',
                'IdentityCard' => str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT), // 9 digits
            ]);

            // Generate simple token
            $token = base64_encode($user->UserID . '|' . time() . '|' . rand(1000, 9999));

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user' => $this->formatUser($user),
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('Email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            // Check password (support both MD5 and bcrypt)
            $passwordMatch = false;

            // First try MD5 (for existing data)
            if (strlen($user->PasswordHash) === 32 && ctype_xdigit($user->PasswordHash)) {
                $passwordMatch = (md5($request->password) === $user->PasswordHash);
            }
            // Then try bcrypt (for new data)
            else if (strlen($user->PasswordHash) === 60 && str_starts_with($user->PasswordHash, '$2y$')) {
                $passwordMatch = Hash::check($request->password, $user->PasswordHash);
            }
            // Plain text password (temporary support)
            else {
                $passwordMatch = ($request->password === $user->PasswordHash);
            }

            if (!$passwordMatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            // Generate simple token
            $token = base64_encode($user->UserID . '|' . time() . '|' . rand(1000, 9999));

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'user' => $this->formatUser($user),
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng nhập'
            ], 500);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Get legacy profile format
     */
    public function profile(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->formatUser($request->user())
            ]);

        } catch (\Exception $e) {
            Log::error('Profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile with role-specific data
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get role-specific profile
            $profileData = null;
            switch ($user->Role) {
                case 'Admin':
                    $profileData = DB::table('profile_admin')->where('UserID', $user->UserID)->first();
                    break;
                case 'Agent':
                    $profileData = DB::table('profile_agent')->where('UserID', $user->UserID)->first();
                    break;
                case 'Customer':
                    $profileData = DB::table('profile_customer')->where('UserID', $user->UserID)->first();
                    break;
                case 'Owner':
                    $profileData = DB::table('profile_owner')->where('UserID', $user->UserID)->first();
                    break;
            }

            $userData = $this->formatUser($user);

            // Add role-specific profile data
            $roleKey = strtolower($user->Role) . '_profile';
            $userData[$roleKey] = $profileData;

            return response()->json([
                'success' => true,
                'data' => $userData
            ]);

        } catch (\Exception $e) {
            Log::error('Get profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin profile'
            ], 500);
        }
    }

    /**
     * Get user transactions (main transactions only)
     */
    public function getTransactions(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $status = $request->input('status');
            $type = $request->input('type');
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = DB::table('transactions')
                ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->leftJoin('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
                ->leftJoin('user as customer', 'transactions.CusID', '=', 'customer.UserID')
                ->leftJoin('user as owner', 'transactions.OwnerID', '=', 'owner.UserID')
                ->select(
                    'transactions.*',
                    'properties.Title as PropertyTitle',
                    'properties.Address as PropertyAddress',
                    'agent.Name as AgentName',
                    'customer.Name as CustomerName',
                    'owner.Name as OwnerName'
                );

            // Filter by user role
            switch ($user->Role) {
                case 'Agent':
                    $query->where('transactions.AgentID', $user->UserID);
                    break;
                case 'Customer':
                    $query->where('transactions.CusID', $user->UserID);
                    break;
                case 'Owner':
                    $query->where('transactions.OwnerID', $user->UserID);
                    break;
                case 'Admin':
                    // Admin can see all transactions
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Không có quyền truy cập'
                    ], 403);
            }

            // Apply filters
            if ($fromDate) {
                $query->where('transactions.TransactionDate', '>=', $fromDate);
            }
            if ($toDate) {
                $query->where('transactions.TransactionDate', '<=', $toDate);
            }
            if ($status) {
                $query->where('transactions.TranStatus', $status);
            }
            if ($type) {
                $query->where('transactions.TransactionType', $type);
            }

            $transactions = $query->orderBy('transactions.TransactionDate', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            // Format transactions with payment details
            $formattedTransactions = $transactions->map(function($transaction) {

                // Get payment details for this transaction
                $paymentDetails = DB::table('detail_transaction')
                    ->where('TransactionID', $transaction->TransactionID)
                    ->orderBy('Num_Pay')
                    ->get();

                $data = [
                    'type' => 'transaction', // Clearly mark as transaction
                    'TransactionID' => $transaction->TransactionID,
                    'CusID' => $transaction->CusID,
                    'OwnerID' => $transaction->OwnerID,
                    'AgentID' => $transaction->AgentID,
                    'PropertyID' => $transaction->PropertyID,
                    'TransactionType' => $transaction->TransactionType,
                    'TransactionDate' => $transaction->TransactionDate,
                    'TotalPrice' => $transaction->TotalPrice,
                    'TranStatus' => $transaction->TranStatus,
                    'PropertyTitle' => $transaction->PropertyTitle,
                    'PropertyAddress' => $transaction->PropertyAddress,
                    'AgentName' => $transaction->AgentName,
                    'CustomerName' => $transaction->CustomerName,
                    'OwnerName' => $transaction->OwnerName,
                    'payment_details' => $paymentDetails->map(function($payment) {
                        return [
                            'Num_Pay' => $payment->Num_Pay,
                            'Price' => $payment->Price,
                            'RentMonth' => $payment->RentMonth,
                            'DTran_Date' => $payment->DTran_Date,
                            'InstallPayment' => $payment->InstallPayment,
                            'PaymentType' => $payment->PaymentType,
                            'DTran_Status' => $payment->DTran_Status,
                        ];
                    })
                ];

                return $data;
            });

            return response()->json([
                'success' => true,
                'data' => $formattedTransactions,
                'message' => 'Danh sách giao dịch bất động sản'
            ]);

        } catch (\Exception $e) {
            Log::error('Get transactions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách giao dịch'
            ], 500);
        }
    }

    /**
     * Get user commissions (for agents only)
     */
    public function getCommissions(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Only agents and admins can view commissions
            if (!in_array($user->Role, ['Agent', 'Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ Agent và Admin mới có thể xem hoa hồng'
                ], 403);
            }

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $status = $request->input('status');
            $type = $request->input('type');
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = DB::table('commission')
                ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
                ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->leftJoin('user as customer', 'transactions.CusID', '=', 'customer.UserID')
                ->leftJoin('user as owner', 'transactions.OwnerID', '=', 'owner.UserID')
                ->select(
                    'commission.*',
                    'transactions.TransactionDate',
                    'transactions.TotalPrice as TransactionAmount',
                    'transactions.TranStatus as TransactionStatus',
                    'properties.Title as PropertyTitle',
                    'properties.Address as PropertyAddress',
                    'customer.Name as CustomerName',
                    'owner.Name as OwnerName'
                );

            // Filter by user role
            if ($user->Role === 'Agent') {
                $query->where('commission.AgentID', $user->UserID);
            }
            // Admin can see all commissions

            // Apply filters
            if ($fromDate) {
                $query->where('transactions.TransactionDate', '>=', $fromDate);
            }
            if ($toDate) {
                $query->where('transactions.TransactionDate', '<=', $toDate);
            }
            if ($status) {
                $query->where('commission.StatusCommission', $status);
            }
            if ($type) {
                $query->where('commission.TypeCom', $type);
            }

            $commissions = $query->orderBy('transactions.TransactionDate', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            // Format commissions
            $formattedCommissions = $commissions->map(function($commission) {
                return [
                    'type' => 'commission', // Clearly mark as commission
                    'CommissionID' => $commission->CommissionID,
                    'TransactionID' => $commission->TransactionID,
                    'AgentID' => $commission->AgentID,
                    'Amount' => $commission->Amount,
                    'Percentage' => $commission->Percentage,
                    'TypeCom' => $commission->TypeCom,
                    'StatusCommission' => $commission->StatusCommission,
                    'PaidDate' => $commission->PaidDate,
                    'TransactionDate' => $commission->TransactionDate,
                    'TransactionAmount' => $commission->TransactionAmount,
                    'TransactionStatus' => $commission->TransactionStatus,
                    'PropertyTitle' => $commission->PropertyTitle,
                    'PropertyAddress' => $commission->PropertyAddress,
                    'CustomerName' => $commission->CustomerName,
                    'OwnerName' => $commission->OwnerName,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedCommissions,
                'message' => 'Danh sách hoa hồng Agent'
            ]);

        } catch (\Exception $e) {
            Log::error('Get commissions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách hoa hồng'
            ], 500);
        }
    }

    /**
     * Get transaction detail
     */
    public function getTransactionDetail(Request $request, $transactionId)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $transaction = DB::table('transactions')
                ->leftJoin('properties', 'transactions.PropertyID', '=', 'properties.PropertyID')
                ->leftJoin('user as agent', 'transactions.AgentID', '=', 'agent.UserID')
                ->leftJoin('user as customer', 'transactions.CusID', '=', 'customer.UserID')
                ->leftJoin('user as owner', 'transactions.OwnerID', '=', 'owner.UserID')
                ->leftJoin('commission', 'transactions.TransactionID', '=', 'commission.TransactionID')
                ->select(
                    'transactions.*',
                    'properties.Title as PropertyTitle',
                    'properties.Address as PropertyAddress',
                    'agent.Name as AgentName',
                    'customer.Name as CustomerName',
                    'owner.Name as OwnerName',
                    'commission.CommissionID',
                    'commission.Amount as CommissionAmount',
                    'commission.Percentage as CommissionPercentage',
                    'commission.StatusCommission',
                    'commission.TypeCom',
                    'commission.PaidDate'
                )
                ->where('transactions.TransactionID', $transactionId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            // Check permission
            $hasPermission = false;
            switch ($user->Role) {
                case 'Admin':
                    $hasPermission = true;
                    break;
                case 'Agent':
                    $hasPermission = $transaction->AgentID === $user->UserID;
                    break;
                case 'Customer':
                    $hasPermission = $transaction->CusID === $user->UserID;
                    break;
                case 'Owner':
                    $hasPermission = $transaction->OwnerID === $user->UserID;
                    break;
            }

            if (!$hasPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền truy cập giao dịch này'
                ], 403);
            }

            // Get payment details
            $paymentDetails = DB::table('detail_transaction')
                ->where('TransactionID', $transactionId)
                ->orderBy('Num_Pay')
                ->get();

            $data = [
                'TransactionID' => $transaction->TransactionID,
                'CusID' => $transaction->CusID,
                'OwnerID' => $transaction->OwnerID,
                'AgentID' => $transaction->AgentID,
                'PropertyID' => $transaction->PropertyID,
                'TransactionType' => $transaction->TransactionType,
                'TransactionDate' => $transaction->TransactionDate,
                'TotalPrice' => $transaction->TotalPrice,
                'TranStatus' => $transaction->TranStatus,
                'PropertyTitle' => $transaction->PropertyTitle,
                'PropertyAddress' => $transaction->PropertyAddress,
                'AgentName' => $transaction->AgentName,
                'CustomerName' => $transaction->CustomerName,
                'OwnerName' => $transaction->OwnerName,
            ];

            // Add commission data if exists
            if ($transaction->CommissionID) {
                $data['commission'] = [
                    'CommissionID' => $transaction->CommissionID,
                    'TransactionID' => $transaction->TransactionID,
                    'AgentID' => $transaction->AgentID,
                    'Amount' => $transaction->CommissionAmount,
                    'Percentage' => $transaction->CommissionPercentage,
                    'StatusCommission' => $transaction->StatusCommission,
                    'TypeCom' => $transaction->TypeCom,
                    'PaidDate' => $transaction->PaidDate,
                ];
            }

            // Add payment details
            $data['payment_details'] = $paymentDetails->map(function($payment) {
                return [
                    'TransactionID' => $payment->TransactionID,
                    'Num_Pay' => $payment->Num_Pay,
                    'Price' => $payment->Price,
                    'RentMonth' => $payment->RentMonth,
                    'DTran_Date' => $payment->DTran_Date,
                    'InstallPayment' => $payment->InstallPayment,
                    'PaymentType' => $payment->PaymentType,
                    'DTran_Status' => $payment->DTran_Status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Get transaction detail error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy chi tiết giao dịch'
            ], 500);
        }
    }

    /**
     * Get transaction statistics
     */
    public function getTransactionStatistics(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $year = $request->input('year', date('Y'));
            $month = $request->input('month');

            $query = DB::table('transactions');

            // Filter by user role
            switch ($user->Role) {
                case 'Agent':
                    $query->where('AgentID', $user->UserID);
                    break;
                case 'Customer':
                    $query->where('CusID', $user->UserID);
                    break;
                case 'Owner':
                    $query->where('OwnerID', $user->UserID);
                    break;
                case 'Admin':
                    // Admin can see all statistics
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Không có quyền truy cập'
                    ], 403);
            }

            // Apply time filters
            $query->whereYear('TransactionDate', $year);
            if ($month) {
                $query->whereMonth('TransactionDate', $month);
            }

            $statistics = $query->selectRaw('
                COUNT(*) as total_transactions,
                SUM(TotalPrice) as total_value,
                SUM(CASE WHEN TranStatus = "Paid" THEN TotalPrice ELSE 0 END) as paid_amount,
                SUM(CASE WHEN TransactionType = "Sale" THEN TotalPrice ELSE 0 END) as sale_value,
                SUM(CASE WHEN TransactionType = "Rent" THEN TotalPrice ELSE 0 END) as rental_value
            ')->first();

            $data = [
                'total_transactions' => $statistics->total_transactions ?? 0,
                'total_value' => $statistics->total_value ?? 0,
                'paid_amount' => $statistics->paid_amount ?? 0,
                'sale_value' => $statistics->sale_value ?? 0,
                'rental_value' => $statistics->rental_value ?? 0,
            ];

            // Add commission data for agents
            if ($user->Role === 'Agent') {
                $commissionStats = DB::table('commission')
                    ->join('transactions', 'commission.TransactionID', '=', 'transactions.TransactionID')
                    ->where('commission.AgentID', $user->UserID)
                    ->whereYear('transactions.TransactionDate', $year);

                if ($month) {
                    $commissionStats->whereMonth('transactions.TransactionDate', $month);
                }

                $commissionData = $commissionStats->selectRaw('
                    SUM(commission.Amount) as total_commission,
                    SUM(CASE WHEN commission.StatusCommission = "Success" THEN commission.Amount ELSE 0 END) as paid_commission,
                    SUM(CASE WHEN commission.StatusCommission = "Pending" THEN commission.Amount ELSE 0 END) as pending_commission
                ')->first();

                $data['commission_data'] = [
                    'total' => $commissionData->total_commission ?? 0,
                    'paid' => $commissionData->paid_commission ?? 0,
                    'pending' => $commissionData->pending_commission ?? 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Get transaction statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thống kê giao dịch'
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20|unique:user,Phone,' . $user->UserID . ',UserID',
                'current_password' => 'sometimes|required|string',
                'password' => 'sometimes|required|string|min:6|confirmed',
                'address' => 'sometimes|string|max:255',
                'ward' => 'sometimes|string|max:100',
                'district' => 'sometimes|string|max:100',
                'province' => 'sometimes|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update basic info
            if ($request->has('name')) {
                $user->Name = $request->name;
            }

            if ($request->has('phone')) {
                $user->Phone = $request->phone;
            }

            if ($request->has('address')) {
                $user->Address = $request->address;
            }

            if ($request->has('ward')) {
                $user->Ward = $request->ward;
            }

            if ($request->has('district')) {
                $user->District = $request->district;
            }

            if ($request->has('province')) {
                $user->Province = $request->province;
            }

            // Update password if provided
            if ($request->has('password')) {
                if (!$request->has('current_password')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng nhập mật khẩu hiện tại'
                    ], 422);
                }

                // Check current password
                $passwordMatch = false;
                if (strlen($user->PasswordHash) === 32 && ctype_xdigit($user->PasswordHash)) {
                    $passwordMatch = (md5($request->current_password) === $user->PasswordHash);
                } else {
                    $passwordMatch = Hash::check($request->current_password, $user->PasswordHash);
                }

                if (!$passwordMatch) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mật khẩu hiện tại không đúng'
                    ], 422);
                }

                $user->PasswordHash = md5($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $this->formatUser($user)
            ]);

        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông tin'
            ], 500);
        }
    }

    /**
     * Update user profile with role-specific data
     */
    public function updateUserProfile(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'birth' => 'sometimes|date',
                'sex' => 'sometimes|string|in:Nam,Nữ,Khác',
                'address' => 'sometimes|string|max:500',
                'ward' => 'sometimes|string|max:255',
                'district' => 'sometimes|string|max:255',
                'province' => 'sometimes|string|max:255',
                // Role-specific fields
                'company_name' => 'sometimes|string|max:255',
                'license_number' => 'sometimes|string|max:100',
                'department' => 'sometimes|string|max:255',
                'preferences' => 'sometimes|string',
                'budget_range' => 'sometimes|string|max:100',
                'property_count' => 'sometimes|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update basic user information
            $userData = array_filter([
                'Name' => $request->name,
                'Phone' => $request->phone,
                'Birth' => $request->birth,
                'Sex' => $request->sex,
                'Address' => $request->address,
                'Ward' => $request->ward,
                'District' => $request->district,
                'Province' => $request->province,
            ]);

            if (!empty($userData)) {
                User::where('UserID', $user->UserID)->update($userData);
            }

            // Update role-specific profile
            $roleProfileData = [];
            switch ($user->Role) {
                case 'Admin':
                    $roleProfileData = array_filter([
                        'Department' => $request->department,
                    ]);
                    if (!empty($roleProfileData)) {
                        DB::table('profile_admin')
                            ->updateOrInsert(
                                ['UserID' => $user->UserID],
                                array_merge($roleProfileData, [
                                    'CreatedAt' => now(),
                                    'UpdatedAt' => now()
                                ])
                            );
                    }
                    break;

                case 'Agent':
                    $roleProfileData = array_filter([
                        'CompanyName' => $request->company_name,
                        'LicenseNumber' => $request->license_number,
                    ]);
                    if (!empty($roleProfileData)) {
                        DB::table('profile_agent')
                            ->updateOrInsert(
                                ['UserID' => $user->UserID],
                                array_merge($roleProfileData, [
                                    'CreatedAt' => now(),
                                    'UpdatedAt' => now()
                                ])
                            );
                    }
                    break;

                case 'Customer':
                    $roleProfileData = array_filter([
                        'Preferences' => $request->preferences,
                        'BudgetRange' => $request->budget_range,
                    ]);
                    if (!empty($roleProfileData)) {
                        DB::table('profile_customer')
                            ->updateOrInsert(
                                ['UserID' => $user->UserID],
                                array_merge($roleProfileData, [
                                    'CreatedAt' => now(),
                                    'UpdatedAt' => now()
                                ])
                            );
                    }
                    break;

                case 'Owner':
                    $roleProfileData = array_filter([
                        'PropertyCount' => $request->property_count,
                    ]);
                    if (!empty($roleProfileData)) {
                        DB::table('profile_owner')
                            ->updateOrInsert(
                                ['UserID' => $user->UserID],
                                array_merge($roleProfileData, [
                                    'CreatedAt' => now(),
                                    'UpdatedAt' => now()
                                ])
                            );
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format user data for API response
     */
    private function formatUser($user)
    {
        return [
            'id' => $user->UserID,
            'name' => $user->Name,
            'email' => $user->Email,
            'phone' => $user->Phone,
            'role' => $user->Role,
            'status' => $user->StatusUser,
            'birth' => $user->Birth,
            'sex' => $user->Sex,
            'identity_card' => $user->IdentityCard,
            'address' => $user->Address,
            'ward' => $user->Ward,
            'district' => $user->District,
            'province' => $user->Province,
            'avatar' => $user->Avatar,
        ];
    }
}
