<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\DanhMucBDS;
use App\Models\DetailProperty;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Transaction;
use App\Models\detail_transaction;
use App\Models\Document;
use App\Models\feedback;
use App\Notifications\NewAppointmentNotification;

class CustomerController extends Controller
{
    public function index()
    {
        // Lấy danh sách danh mục BĐS để hiển thị trong dropdown tìm kiếm
        $danhmucs = DanhMucBDS::all();

        // Lấy BĐS dành cho bạn (4 BĐS mới nhất đã được duyệt)
        $recentProperties = Property::with(['danhMuc', 'chiTiet'])
            ->where('Status', 1)
            ->orderBy('PostedDate', 'desc')
            ->limit(4)
            ->get();

        // Lấy BĐS nổi bật (3 BĐS có giá cao nhất đã được duyệt)
        $featuredProperties = Property::with(['danhMuc', 'chiTiet'])
            ->where('Status', 1)
            ->orderBy('Price', 'desc')
            ->limit(3)
            ->get();

        return view('trangchu.index', compact('danhmucs', 'recentProperties', 'featuredProperties'));
    }

    public function showProfile()
    {
        return view('_layout._layhome.profile');
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,Email,' . $user->UserID . ',UserID'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $user->Name = $request->name;
        $user->Email = $request->email;
        $user->Phone = $request->phone;
        $user->Address = $request->address;
        $user->save();

        return redirect()->route('customer.profile')->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }

    public function showChangePasswordForm()
    {
        return view('_layout._layhome.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'PasswordHash' => Hash::make($request->password) // Sử dụng Hash::make thay vì md5
        ]);

        return redirect()->route('customer.change-password')
            ->with('success', 'Mật khẩu đã được thay đổi thành công.');
    }

    public function search(Request $request)
    {
        // Debug request
        Log::info('Search request:', $request->all());

        // Base query với eager loading
        $query = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('Status', 'active'); // Chỉ lấy BĐS đã được duyệt (active)

        Log::info('Initial Status filter: active');

        // Tìm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            Log::info('Searching with keyword: ' . $keyword);

            $query->where(function($q) use ($keyword) {
                $q->where('Province', 'like', '%'.$keyword.'%')
                  ->orWhere('District', 'like', '%'.$keyword.'%')
                  ->orWhere('Ward', 'like', '%'.$keyword.'%')
                  ->orWhere('Address', 'like', '%'.$keyword.'%')
                  ->orWhere('Title', 'like', '%'.$keyword.'%');
            });
        }

        // 2. Tìm theo loại hình BĐS (từ bảng danhmuc_pro)
        if ($request->filled('type')) {
            $query->where('PropertyType', $request->type);
        }

        // 3. Tìm theo giá
        if ($request->filled('price')) {
            switch ($request->price) {
                case '1': // Dưới 1 tỷ
                    $query->where('Price', '<', 1000000000);
                    break;
                case '2': // 1-3 tỷ
                    $query->whereBetween('Price', [1000000000, 3000000000]);
                    break;
                case '3': // 3-5 tỷ
                    $query->whereBetween('Price', [3000000000, 5000000000]);
                    break;
                case '4': // Trên 5 tỷ
                    $query->where('Price', '>', 5000000000);
                    break;
            }
        }

        // 4. Tìm theo diện tích (từ bảng detail_pro)
        if ($request->filled('area')) {
            $query->whereHas('chiTiet', function($q) use ($request) {
                switch ($request->area) {
                    case '1': // Dưới 30m²
                        $q->where('Area', '<', 30);
                        break;
                    case '2': // 30-50m²
                        $q->whereBetween('Area', [30, 50]);
                        break;
                    case '3': // 50-80m²
                        $q->whereBetween('Area', [50, 80]);
                        break;
                    case '4': // Trên 80m²
                        $q->where('Area', '>', 80);
                        break;
                }
            });
        }

        // Debug final query
        Log::info('Final SQL: ' . $query->toSql());
        Log::info('SQL Bindings:', $query->getBindings());

        // Execute query
        $properties = $query->paginate(12);

        Log::info('Found ' . $properties->total() . ' active properties');

        return view('trangchu.search-results', [
            'properties' => $properties,
            'danhmucs' => DanhMucBDS::all(),
            'debugInfo' => [
                'Status Filter' => 'active',
                'Keyword' => $request->keyword ?? 'Not specified',
                'Type' => $request->type ? DanhMucBDS::find($request->type)->ten_pro : 'Not specified',
                'Price Range' => $request->price ? $this->getPriceRangeText($request->price) : 'Not specified',
                'Area Range' => $request->area ? $this->getAreaRangeText($request->area) : 'Not specified',
                'Total Results' => $properties->total()
            ]
        ]);
    }

    private function getPriceRangeText($range)
    {
        switch ($range) {
            case '1': return 'Dưới 1 tỷ';
            case '2': return '1-3 tỷ';
            case '3': return '3-5 tỷ';
            case '4': return 'Trên 5 tỷ';
            default: return 'Unknown';
        }
    }

    private function getAreaRangeText($range)
    {
        switch ($range) {
            case '1': return 'Dưới 30m²';
            case '2': return '30-50m²';
            case '3': return '50-80m²';
            case '4': return 'Trên 80m²';
            default: return 'Unknown';
        }
    }

    public function propertyDetail($propertyID)
    {
        try {
            // Debug
            Log::info('Accessing property detail with ID: ' . $propertyID);

            // Tìm property theo ID và đảm bảo load các relationships
            $property = Property::with(['danhMuc', 'chiTiet', 'chusohuu', 'moigioi', 'images'])
                ->where('PropertyID', $propertyID)
                ->where('Status', 'active')
                ->firstOrFail();

            Log::info('Found property: ' . $property->Title);

            // Lấy các BĐS liên quan
            $relatedProperties = Property::where('Status', 'active')
                ->where('PropertyID', '!=', $propertyID)
                ->where(function($query) use ($property) {
                    $query->where('PropertyType', $property->PropertyType)
                        ->orWhere('District', $property->District);
                })
                ->with(['danhMuc', 'chiTiet', 'images'])
                ->limit(3)
                ->get();

            return view('trangchu.property-detail', compact('property', 'relatedProperties'));
        } catch (\Exception $e) {
            Log::error('Error in propertyDetail: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Không tìm thấy bất động sản này');
        }
    }

    public function showAppointments()
    {
        // Debug authentication
        $user = Auth::user();
        // Log::info('showAppointments called', [
        //     'auth_check' => Auth::check(),
        //     'user_id' => Auth::id(),
        //     'user' => $user ? $user->toArray() : null
        // ]);

        if (!Auth::check()) {
            Log::error('User not authenticated in showAppointments');
            return redirect()->route('login')->withErrors(['error' => 'Bạn cần đăng nhập để xem lịch hẹn']);
        }

        $appointments = Appointment::with(['property', 'user_agent', 'user_owner'])
            ->where('CusID', $user->UserID)  // Sử dụng $user->UserID thay vì Auth::id()
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        Log::info('Appointments found', [
            'count' => $appointments->count(),
            'user_id' => $user->UserID
        ]);

        return view('customer.appointments.show', compact('appointments'));
    }

    public function getNotifications()
    {
        $user = Auth::user();
        $appointments = Appointment::with(['property', 'user_agent'])
            ->where('CusID', $user->UserID)
            ->where('Status', 'pending')  // Only show pending appointments
            ->orderBy('AppointmentDateStart', 'desc')
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->AppointmentID,
                    'data' => [
                        'agent_name' => $appointment->user_agent->Name,
                        'property_title' => $appointment->property->Title,
                        'appointment_date' => $appointment->AppointmentDateStart
                    ],
                    'created_at' => $appointment->AppointmentDateStart
                ];
            });

        return response()->json([
            'notifications' => $appointments,
            'unreadCount' => $appointments->count()
        ]);
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $appointment = Appointment::where('AppointmentID', $id)
            ->where('CusID', $user->UserID)
            ->first();

        if ($appointment) {
            $appointment->Status = 'read';
            $appointment->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function saleProperties(Request $request)
    {
        // Lấy danh sách danh mục BĐS để hiển thị trong dropdown tìm kiếm
        $danhmucs = DanhMucBDS::all();

        // Base query cho BĐS bán
        $query = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('Status', 'active')
            ->where('TypePro', 'Sale'); // Lọc theo loại "Bán"

        // Áp dụng các bộ lọc tìm kiếm
        $query = $this->applySearchFilters($query, $request);

        // Phân trang - hiển thị 15 bất động sản mỗi trang
        $properties = $query->paginate(15);

        return view('trangchu.propertylist', [
            'properties' => $properties,
            'danhmucs' => $danhmucs,
            'pageTitle' => 'Bất động sản mua bán',
            'pageType' => 'sale'
        ]);
    }

    public function rentProperties(Request $request)
    {
        // Lấy danh sách danh mục BĐS để hiển thị trong dropdown tìm kiếm
        $danhmucs = DanhMucBDS::all();

        // Base query cho BĐS cho thuê
        $query = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('Status', 'active')
            ->where('TypePro', 'Rent'); // Lọc theo loại "Thuê"

        // Áp dụng các bộ lọc tìm kiếm
        $query = $this->applySearchFilters($query, $request);

        // Phân trang - hiển thị 15 bất động sản mỗi trang
        $properties = $query->paginate(15);

        return view('trangchu.propertylist', [
            'properties' => $properties,
            'danhmucs' => $danhmucs,
            'pageTitle' => 'Bất động sản cho thuê',
            'pageType' => 'rent',
            'breadcrumb' => 'Cho thuê'
        ]);
    }

    private function applySearchFilters($query, Request $request)
    {
        // Tìm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('Province', 'like', '%'.$keyword.'%')
                  ->orWhere('District', 'like', '%'.$keyword.'%')
                  ->orWhere('Ward', 'like', '%'.$keyword.'%')
                  ->orWhere('Address', 'like', '%'.$keyword.'%')
                  ->orWhere('Title', 'like', '%'.$keyword.'%');
            });
        }

        // Tìm theo loại hình BĐS
        if ($request->filled('type')) {
            $query->where('PropertyType', $request->type);
        }

        // Tìm theo phòng ngủ
        if ($request->filled('bedrooms')) {
            $bedrooms = $request->bedrooms;
            $query->whereHas('chiTiet', function($q) use ($bedrooms) {
                if ($bedrooms == 4) {
                    // 4+ phòng ngủ
                    $q->where('Bedroom', '>=', 4);
                } else {
                    // Chính xác 1, 2 hoặc 3 phòng ngủ
                    $q->where('Bedroom', $bedrooms);
                }
            });
        }

        // Tìm theo phạm vi giá (sử dụng thanh trượt)
        if ($request->filled('price_min') && $request->filled('price_max')) {
            $minPrice = (int)$request->price_min;
            $maxPrice = (int)$request->price_max;

            if ($minPrice > 0 || $maxPrice < 10000) {
                $query->whereBetween('Price', [$minPrice * 1000000, $maxPrice * 1000000]);
            }
        }

        // Tìm theo giá (hỗ trợ cả phương thức cũ)
        if ($request->filled('price')) {
            switch ($request->price) {
                case '1':
                    $query->where('Price', '<', 1000000000);
                    break;
                case '2':
                    $query->whereBetween('Price', [1000000000, 3000000000]);
                    break;
                case '3':
                    $query->whereBetween('Price', [3000000000, 5000000000]);
                    break;
                case '4':
                    $query->where('Price', '>', 5000000000);
                    break;
            }
        }

        return $query;
    }

    public function cancelAppointment($id)
    {
        $user = Auth::user();
        $appointment = Appointment::where('AppointmentID', $id)
            ->where('CusID', $user->UserID)
            ->whereIn('Status', ['Pending', 'Confirmed'])
            ->first();

        if ($appointment) {
            $appointment->Status = 'Cancelled';
            $appointment->save();
            return response()->json(['success' => true, 'message' => 'Lịch hẹn đã được hủy thành công']);
        }

        return response()->json(['success' => false, 'message' => 'Không thể hủy lịch hẹn này'], 404);
    }

    public function transactionHistory()
    {
        $user = Auth::user();

        // Debug: Kiểm tra tất cả giao dịch của user trước
        $allTransactions = Transaction::where('CusID', $user->UserID)->get();
        Log::info('All user transactions:', [
            'user_id' => $user->UserID,
            'total_count' => $allTransactions->count(),
            'transactions' => $allTransactions->map(function($t) {
                return [
                    'id' => $t->TransactionID,
                    'status' => $t->TranStatus,
                    'date' => $t->TransactionDate
                ];
            })
        ]);

        // Lấy TẤT CẢ giao dịch để debug (tạm thời bỏ filter Pending)
        $transactions = Transaction::with([
            'detailTransaction' => function($query) {
                $query->orderBy('DTran_Date', 'asc');
            },
            'trans_property.danhMuc',
            'trans_property.chiTiet',
            'trans_agent',
            'trans_owner'
        ])
        ->where('CusID', $user->UserID)
        // ->where('TranStatus', 'Pending') // Tạm thời comment để xem tất cả
        ->orderBy('TransactionDate', 'asc')
        ->get()
        ->map(function($transaction) {
            // Sắp xếp detail_transaction: Chờ đợi trước, Hoàn Thành sau
            $details = $transaction->detailTransaction->sort(function($a, $b) {
                // Nếu status khác nhau, Chờ đợi lên trước
                if ($a->DTran_Status !== $b->DTran_Status) {
                    return $a->DTran_Status === 'Hoàn Thành' ? 1 : -1;
                }
                // Nếu cùng status, sắp xếp theo ngày tăng dần
                return strtotime($a->DTran_Date) - strtotime($b->DTran_Date);
            });
            $transaction->setRelation('detailTransaction', $details);
            return $transaction;
        });

        Log::info('Filtered transactions:', [
            'count' => $transactions->count(),
            'transactions' => $transactions->map(function($t) {
                return [
                    'id' => $t->TransactionID,
                    'status' => $t->TranStatus,
                    'details_count' => $t->detailTransaction->count()
                ];
            })
        ]);

        return view('customer.history_tran.index', compact('transactions'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'num_pay' => 'required',
            'payment_method' => 'required|in:momo,vnpay'
        ]);

        // Xử lý thanh toán tùy theo phương thức
        $transactionId = $request->transaction_id;
        $numPay = $request->num_pay;
        $paymentMethod = $request->payment_method;

        // Tìm detail transaction
        $detailTransaction = detail_transaction::where('TransactionID', $transactionId)
            ->where('Num_Pay', $numPay)
            ->first();

        if (!$detailTransaction) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy giao dịch']);
        }

        // Redirect đến trang thanh toán tương ứng
        if ($paymentMethod === 'momo') {
            return $this->processMomoPayment($detailTransaction);
        } else {
            return $this->processVnpayPayment($detailTransaction);
        }
    }

    private function processMomoPayment($detailTransaction)
    {
        // Logic xử lý thanh toán Momo
        // Tạm thời return success để demo
        return response()->json([
            'success' => true,
            'redirect_url' => 'https://momo.vn/payment',
            'message' => 'Đang chuyển hướng đến Momo...'
        ]);
    }

    private function processVnpayPayment($detailTransaction)
    {
        // Logic xử lý thanh toán VNPay
        // Tạm thời return success để demo
        return response()->json([
            'success' => true,
            'redirect_url' => 'https://vnpay.vn/payment',
            'message' => 'Đang chuyển hướng đến VNPay...'
        ]);
    }

    public function transactionDetail($transactionId)
    {
        $user = Auth::user();

        // Lấy chi tiết giao dịch với các relationship
        $transaction = Transaction::with([
            'detailTransaction' => function($query) {
                $query->orderBy('DTran_Date', 'asc');
            },
            'trans_property.danhMuc',
            'trans_property.chiTiet',
            'trans_property.images',
            'trans_agent.profile_agent',
            'trans_owner.profile_owner'
        ])
        ->where('TransactionID', $transactionId)
        ->where('CusID', $user->UserID)
        ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Không tìm thấy giao dịch'], 404);
        }

        // Lấy documents liên quan đến giao dịch
        $documents = Document::where('TransactionID', $transactionId)->get();

        return view('customer.history_tran.detail', compact('transaction', 'documents'));
    }    /**
     * View document for customer - Only allow viewing documents from their own transactions
     */
    public function viewDocument($documentId)
    {
        $user = Auth::user();

        // Kiểm tra document có thuộc về giao dịch của customer không
        $document = Document::with('doc_transaction')
            ->where('DocumentID', $documentId)
            ->whereHas('doc_transaction', function($query) use ($user) {
                $query->where('CusID', $user->UserID);
            })
            ->first();

        if (!$document) {
            abort(404, 'Không tìm thấy tài liệu hoặc bạn không có quyền truy cập');
        }

        // Lấy file từ đúng path: storage/app/documents/trans_{transactionID}
        $transactionId = $document->doc_transaction->TransactionID;
        $filePath = storage_path("app/documents/{$transactionId}/" . basename($document->FilePath));

        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại tại đường dẫn: ' . $filePath);
        }

        // Get file info
        $fileInfo = pathinfo($filePath);
        $extension = strtolower($fileInfo['extension']);

        // Nếu là file Word, sử dụng phpoffice/phpword để đọc và hiển thị
        if (in_array($extension, ['doc', 'docx'])) {
            try {
                // Import PhpWord
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);

                // Convert to HTML for viewing
                $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

                // Get HTML content
                ob_start();
                $htmlWriter->save('php://output');
                $htmlContent = ob_get_clean();

                // Return HTML view
                return response($htmlContent)->header('Content-Type', 'text/html; charset=UTF-8');

            } catch (\Exception $e) {
                // Fallback: download file if can't read
                return response()->download($filePath, $document->DocumentName);
            }
        }

        // Set appropriate content type for other files
        $contentType = match($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            default => 'application/octet-stream'
        };

        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $document->DocumentName . '"'
        ]);
    }

    /**
     * Download document for customer - Only allow downloading documents from their own transactions
     */
    public function downloadDocument($documentId)
    {
        $user = Auth::user();

        // Kiểm tra document có thuộc về giao dịch của customer không
        $document = Document::with('doc_transaction')
            ->where('DocumentID', $documentId)
            ->whereHas('doc_transaction', function($query) use ($user) {
                $query->where('CusID', $user->UserID);
            })
            ->first();

        if (!$document) {
            abort(404, 'Không tìm thấy tài liệu hoặc bạn không có quyền truy cập');
        }

        // Lấy file từ đúng path: storage/app/documents/trans_{transactionID}
        $transactionId = $document->doc_transaction->TransactionID;
        $filePath = storage_path("app/documents/{$transactionId}/" . basename($document->FilePath));

        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại tại đường dẫn: ' . $filePath);
        }

        // Get file extension to ensure proper filename
        $fileInfo = pathinfo($filePath);
        $extension = $fileInfo['extension'] ?? '';
        $downloadName = $document->DocumentName;

        // Add extension if not present in document name
        if ($extension && !str_ends_with($downloadName, '.' . $extension)) {
            $downloadName .= '.' . $extension;
        }

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    /**
     * Display agent directory with ratings and contact info
     */
    public function contactAgent(Request $request)
    {
        $user = Auth::user();

        // Base query for agents - include all agents but eagerly load profile_agent
        $query = User::with(['profile_agent'])
            ->where('Role', 'Agent')
            ->where('StatusUser', 'active');

        // Debug: Log total agents before filters
        Log::info('Total active agents: ' . $query->count());

        // Search by name, email, phone or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                  ->orWhere('Email', 'LIKE', "%{$search}%")
                  ->orWhere('Phone', 'LIKE', "%{$search}%")
                  ->orWhere('Address', 'LIKE', "%{$search}%")
                  ->orWhere('District', 'LIKE', "%{$search}%")
                  ->orWhere('Province', 'LIKE', "%{$search}%")
                  ->orWhereHas('profile_agent', function($subQuery) use ($search) {
                      $subQuery->where('ProvinceAgent', 'LIKE', "%{$search}%")
                               ->orWhere('DistrictAgent', 'LIKE', "%{$search}%")
                               ->orWhere('Certificate', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by province (check both user.Province and profile_agent.ProvinceAgent)
        if ($request->filled('province')) {
            $query->where(function($q) use ($request) {
                $q->where('Province', $request->province)
                  ->orWhereHas('profile_agent', function($subQuery) use ($request) {
                      $subQuery->where('ProvinceAgent', $request->province);
                  });
            });
        }

        // Filter by district (check both user.District and profile_agent.DistrictAgent)
        if ($request->filled('district')) {
            $query->where(function($q) use ($request) {
                $q->where('District', $request->district)
                  ->orWhereHas('profile_agent', function($subQuery) use ($request) {
                      $subQuery->where('DistrictAgent', $request->district);
                  });
            });
        }

        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->min_rating;
            $query->whereHas('agent_feedbacks', function($q) use ($minRating) {
                $q->where('Status', 'Đã duyệt')
                  ->havingRaw('AVG(Rating) >= ?', [$minRating]);
            }, '>=', 1);
        }

        // Get agents with feedback statistics using subqueries for better performance
        $agents = $query->withCount(['agent_feedbacks as total_ratings' => function($query) {
                $query->where('Status', 'Đã duyệt');
            }])
            ->addSelect([
                'average_rating' => feedback::selectRaw('COALESCE(ROUND(AVG(Rating), 1), 0)')
                    ->whereColumn('AgentID', 'user.UserID')
                    ->where('Status', 'Đã duyệt')
            ]);

        // Sort by rating if requested
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'rating_desc':
                    $agents = $agents->orderByDesc('average_rating')->orderByDesc('total_ratings');
                    break;
                case 'name_asc':
                    $agents = $agents->orderBy('Name');
                    break;
                case 'newest':
                    $agents = $agents->orderBy('created_at', 'desc');
                    break;
                case 'rating':
                    $agents = $agents->orderByDesc('average_rating')->orderByDesc('total_ratings');
                    break;
                case 'name':
                    $agents = $agents->orderBy('Name');
                    break;
                default:
                    $agents = $agents->orderBy('Name');
            }
        } else {
            $agents = $agents->orderBy('Name');
        }

        $agents = $agents->get();

        // Debug: Check if profile_agent data is loaded
        Log::info('Sample agent with profile_agent data:');
        if ($agents->count() > 0) {
            $firstAgent = $agents->first();
            Log::info('Agent ID: ' . $firstAgent->UserID);
            Log::info('Agent Name: ' . $firstAgent->Name);
            Log::info('Profile Agent exists: ' . ($firstAgent->profile_agent ? 'YES' : 'NO'));
            if ($firstAgent->profile_agent) {
                Log::info('Province Agent: ' . $firstAgent->profile_agent->ProvinceAgent);
                Log::info('District Agent: ' . $firstAgent->profile_agent->DistrictAgent);
            }
        }

        // Get unique provinces for filter (from both user table and profile_agent)
        $userProvinces = User::where('Role', 'Agent')
            ->where('StatusUser', 'active')
            ->whereNotNull('Province')
            ->distinct()
            ->pluck('Province');

        $agentProvinces = User::where('Role', 'Agent')
            ->where('StatusUser', 'active')
            ->whereHas('profile_agent', function($q) {
                $q->whereNotNull('ProvinceAgent');
            })
            ->with('profile_agent')
            ->get()
            ->pluck('profile_agent.ProvinceAgent')
            ->filter();

        $provinces = $userProvinces->merge($agentProvinces)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Get unique districts for filter
        $userDistricts = User::where('Role', 'Agent')
            ->where('StatusUser', 'active')
            ->whereNotNull('District')
            ->distinct()
            ->pluck('District');

        $agentDistricts = User::where('Role', 'Agent')
            ->where('StatusUser', 'active')
            ->whereHas('profile_agent', function($q) {
                $q->whereNotNull('DistrictAgent');
            })
            ->with('profile_agent')
            ->get()
            ->pluck('profile_agent.DistrictAgent')
            ->filter();

        $districts = $userDistricts->merge($agentDistricts)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Paginate manually since we processed the collection
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedAgents = $agents->slice($offset, $perPage)->values();

        return view('customer.contactagent.index', [
            'agents' => $paginatedAgents,
            'provinces' => $provinces,
            'districts' => $districts,
            'totalAgents' => $agents->count(),
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'hasMorePages' => $agents->count() > ($currentPage * $perPage),
            'request' => $request
        ]);
    }

    /**
     * Submit feedback for an agent
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:user,UserID',
            'title' => 'required|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();

        // Check if user has already submitted feedback for this agent
        $existingFeedback = feedback::where('CusID', $user->UserID)
            ->where('AgentID', $request->agent_id)
            ->first();

        if ($existingFeedback) {
            return back()->with('error', 'Bạn đã đánh giá agent này rồi.');
        }

        feedback::create([
            'CusID' => $user->UserID,
            'AgentID' => $request->agent_id,
            'Title' => $request->title,
            'Rating' => $request->rating,
            'Comment' => $request->comment,
            'FeedbackDate' => now(),
            'Status' => 'Chờ duyệt'
        ]);

        return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
    }
}
