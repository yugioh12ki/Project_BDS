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
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function dashboard()
    {
        return view('owners.dashboard');
    }

    public function listProperty()
    {
        // Lấy tất cả bất động sản từ database, không lọc theo OwnerID
        $properties = Property::with(['danhMuc', 'chiTiet', 'images'])->get();
        
        // Debug để kiểm tra dữ liệu
        // dd($properties);
        
        $categories = DanhMucBDS::all();
        $owners = User::all();

        // Lấy properties của chủ sở hữu hiện tại cho modal "Tạo tin đăng"
        $ownerId = Auth::user()->UserID;
        $ownerProperties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->get();

        return view('owners.property.index', compact('properties', 'categories', 'owners', 'ownerProperties'));
    }


    // public function createProperty()
    // {
    //     $categories = DanhMucBDS::all();
    //     return view('property.create_proper', compact('categories'));
    // }

    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Address' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'Province' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Ward' => 'required|string|max:255',
            'PropertyType' => 'required|integer',
            'TypePro' => 'required|string', 

            'Floor' => 'required|integer|min:0',
            'Area' => 'required|integer|min:1',
            'Bedroom' => 'required|integer|min:0',
            'Bath_WC' => 'required|integer|min:0',
            'Road' => 'required|integer|min:0',
 
            'legal' => 'nullable|string|max:255',
            'view' => 'nullable|string|max:255',
            'near' => 'nullable|string|max:255',
            'Interior' => 'nullable|string|max:255',
            'WaterPrice' => 'nullable|string|max:255',
            'PowerPrice' => 'nullable|string|max:255',
            'Utilities' => 'nullable|string|max:255',
            'Description' => 'nullable|string'
        ]);

        $detail = new DetailProperty();
        $detail->Floor = $validated['Floor'];
        $detail->Area = $validated['Area'];
        $detail->Bedroom = $validated['Bedroom'];
        $detail->Bath_WC = $validated['Bath_WC'];
        $detail->Road = $validated['Road'];
        $detail->legal = $validated['legal'] ?? null;
        $detail->view = $validated['view'] ?? null;
        $detail->near = $validated['near'] ?? null;
        $detail->Interior = $validated['Interior'] ?? null;
        $detail->WaterPrice = $validated['WaterPrice'] ?? null;
        $detail->PowerPrice = $validated['PowerPrice'] ?? null;
        $detail->Utilities = $validated['Utilities'] ?? null;
        $detail->save();

        $idDetail = $detail->IdDetail;
        $property = new Property();
        $property->Title = $validated['Title'];
        $property->Address = $validated['Address'];
        $property->Price = $validated['Price'];
        $property->Province = $validated['Province'];
        $property->District = $validated['District'];
        $property->Ward = $validated['Ward'];
        $property->PropertyType = $validated['PropertyType'];
        $property->TypePro = $validated['TypePro'];
        $property->Description = $validated['Description'] ?? null;
        $property->IdDetail = $idDetail;
        $property->OwnerID = Auth::user()->UserID;
        $property->PostedDate = now()->format('Y-m-d');
        $property->Status = 'inactive'; 
        $property->save();

        return redirect()->route('owner.property.index')->with('success', 'Thêm bất động sản thành công!');
    }


    public function appointments()
    {
        $ownerId = Auth::user()->UserID;
        
        // Lấy danh sách lịch hẹn liên quan đến chủ nhà
        $appointments = Appointment::where('OwnerID', $ownerId)
            ->with(['property', 'user_agent', 'user_customer'])
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();
            
        // Phân loại các cuộc hẹn
        $upcomingAppointments = $appointments->filter(function($appointment) {
            return $appointment->AppointmentDateStart >= Carbon::today() && 
                  ($appointment->Status == 'pending' || 
                   $appointment->Status == 'confirmed');
        });
        
        $completedAppointments = $appointments->filter(function($appointment) {
            return $appointment->Status == 'completed' || $appointment->Status == 'Hoàn Thành';
        });
        
        $cancelledAppointments = $appointments->filter(function($appointment) {
            return $appointment->Status == 'cancelled' || $appointment->Status == 'Đã Hủy';
        });

        return view('owners.appointment.appointments', compact(
            'appointments', 
            'upcomingAppointments', 
            'completedAppointments', 
            'cancelledAppointments'
        ));
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
    
    /**
     * Lấy danh sách bất động sản cho modal tạo tin đăng
     */
    public function getPropertiesForListing()
    {
        $ownerId = Auth::user()->UserID;
        $properties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->whereNull('ApprovedBy')
            ->where('Status', 'inactive')
            ->get();
            
        return response()->json($properties);
    }

    /**
     * Lưu tin đăng ký gửi bất động sản mới
     */
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
}
