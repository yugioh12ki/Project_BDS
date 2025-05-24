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
        return view('owners.dashboard');
    }

    public function listProperty()
    {
        $ownerId = Auth::user()->UserID;
        
        // Lấy tất cả BĐS của owner hiện tại
        $properties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->get();

        // Lấy BĐS pending để hiển thị trong modal tạo listing
        $ownerProperties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->where('Status', 'pending')
            ->get();

        $categories = DanhMucBDS::all();
        $owners = User::all();

        // Log để debug
        \Log::info('Properties count: ' . $properties->count());
        \Log::info('Pending properties count: ' . $ownerProperties->count());

        return view('owners.property.index', compact(
            'properties',       
            'ownerProperties', 
            'categories',
            'owners'
        ));
    }


    public function createPropertyForm()
    {
        $property = null; // Khởi tạo biến property
        // biến $Owner lấy giá trị là id của người dùng hiện tại
        $Owner = Auth::user()->UserID;
        $categories = DanhMucBDS::all();
        return view('_system.partialview.create_property', compact('property','Owner','categories'));
    }

    public function createProperty(Request $request)
    {
        try {
            // Validate main property data
            $validatedProperty = $request->validate([
                'Title' => 'required|string|max:255',
                'TypePro' => 'required|in:Sale,Rent',
                'Description' => 'required|string',
                'Price' => 'required|numeric|min:0',
                'Address' => 'required|string|max:255',
                'Ward' => 'required|string|max:255',
                'District' => 'required|string|max:255',
                'Province' => 'required|string|max:255',
                'PropertyType' => 'required|exists:danhmuc_pro,Protype_ID',
            ]);

            $property_ID = uniqid('PR');
            $ownerID = Auth::user()->UserID;

            // Validate property details
            $validatedDetails = $request->validate([
                'Levelhouse'   => 'nullable|integer',
                'Floor'        => 'nullable|integer',
                'HouseLength'  => 'nullable|integer',
                'HouseWidth'   => 'nullable|integer',
                'TotalLength'  => 'nullable|integer',
                'TotalWidth'   => 'nullable|integer',
                'Bedroom'      => 'nullable|integer',
                'Balcony'      => 'nullable|boolean',
                'Bath_WC'      => 'nullable|integer',
                'Road'         => 'nullable|integer',
                'legal'        => 'nullable|string|max:255',
                'view'         => 'nullable|string',
                'near'         => 'nullable|string|max:255',
                'Interior'     => 'nullable|string',
                'WaterPrice'   => 'nullable|string',
                'PowerPrice'   => 'nullable|string',
                'Utilities'    => 'nullable|string',
            ]);

            // Create new property entry
            $property = new Property();
            $property->PropertyID   = $property_ID;
            $property->Title = $validatedProperty['Title'];
            $property->TypePro = $validatedProperty['TypePro'];
            $property->Description = $validatedProperty['Description'];
            $property->Price = $validatedProperty['Price'];
            $property->Address = $validatedProperty['Address'];
            $property->Ward = $validatedProperty['Ward'];
            $property->District = $validatedProperty['District'];
            $property->Province = $validatedProperty['Province'];
            $property->PropertyType = $validatedProperty['PropertyType'];
            $property->OwnerID = Auth::user()->UserID;
            $property->AgentID =  null;
            $property->PostedDate = now();
            $property->ApprovedBy = null;
            $property->ApprovedDate = now();
            $property->Status = 'pending'; // New properties are pending by default


            // Save the property to get PropertyID
            $property->save();

            // Create property details
            $propertyDetail = new DetailProperty();
            $propertyDetail->PropertyID = $property_ID;
            $propertyDetail->Levelhouse   = $request->input('Levelhouse');
            $propertyDetail->Floor        = $request->input('Floor');
            $propertyDetail->HouseLength  = $request->input('HouseLength');
            $propertyDetail->HouseWidth   = $request->input('HouseWidth');
            $propertyDetail->TotalLength  = $request->input('TotalLength');
            $propertyDetail->TotalWidth   = $request->input('TotalWidth');
            $propertyDetail->Bedroom      = $request->input('Bedroom');
            $propertyDetail->Balcony      = $request->input('Balcony');
            $propertyDetail->Bath_WC      = $request->input('Bath_WC');
            $propertyDetail->Road         = $request->input('Road');
            $propertyDetail->legal        = $request->input('legal');
            $propertyDetail->view         = $request->input('view');
            $propertyDetail->near         = $request->input('near');
            $propertyDetail->Interior     = $request->input('Interior');
            $propertyDetail->WaterPrice   = $request->input('WaterPrice');
            $propertyDetail->PowerPrice   = $request->input('PowerPrice');
            $propertyDetail->Utilities    = $request->input('Utilities');

            $propertyDetail->save();

            if ($request->hasFile('property_images')) {
                foreach ($request->file('property_images') as $imageFile) {
                    if ($imageFile->isValid()) {
                        $img = new Image();
                        $img->PropertyID = $property_ID;
                        $img->ImagePath = file_get_contents($imageFile->getRealPath());
                        $img->Caption = null;
                        $img->UploadedDate = now();
                        $img->save();
                    }
                }
            }

            if ($request->hasFile('property_video')) {
                $videoFile = $request->file('property_video');
                if ($videoFile->isValid()) {
                    $video = new Video();
                    $video->PropertyID = $property_ID;
                    $video->VideoPath = file_get_contents($videoFile->getRealPath());
                    $video->Description = null;
                    $video->UploadedDate = now();
                    $video->save();
                }
            }

            return redirect()->route('owner.property.create')->with('success', 'Bất động sản đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Lỗi khi tạo bất động sản: ' . $e->getMessage()]);
        }
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

            \Log::info('Found ' . $ownerProperties->count() . ' pending properties for owner ' . $ownerId);

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
            \Log::error('Error in getPropertiesForListing: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi tải dữ liệu'], 500);
        }
    }
}
