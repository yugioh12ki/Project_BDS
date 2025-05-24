<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Property;
use App\Models\DanhMucBDS;
use App\Models\DetailProperty;

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
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'Name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

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

        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('customer.change-password')
            ->with('success', 'Mật khẩu đã được thay đổi thành công.');
    }

    public function search(Request $request)
    {
        // Debug request
        \Log::info('Search request:', $request->all());

        // Base query với eager loading
        $query = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('Status', 'active'); // Chỉ lấy BĐS đã được duyệt (active)

        \Log::info('Initial Status filter: active');

        // Tìm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            \Log::info('Searching with keyword: ' . $keyword);
            
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
        \Log::info('Final SQL: ' . $query->toSql());
        \Log::info('SQL Bindings:', $query->getBindings());

        // Execute query
        $properties = $query->paginate(12);

        \Log::info('Found ' . $properties->total() . ' active properties');

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

    public function propertyDetail($id)
    {
        try {
            // Debug
            \Log::info('Accessing property detail with ID: ' . $id);
            
            // Tìm property theo ID và đảm bảo load các relationships
            $property = Property::with(['danhMuc', 'chiTiet', 'chusohuu', 'moigioi', 'images'])
                ->where('PropertyID', $id)
                ->where('Status', 'active')
                ->firstOrFail();

            \Log::info('Found property: ' . $property->Title);

            // Lấy các BĐS liên quan
            $relatedProperties = Property::where('Status', 'active')
                ->where('PropertyID', '!=', $id)
                ->where(function($query) use ($property) {
                    $query->where('PropertyType', $property->PropertyType)
                        ->orWhere('District', $property->District);
                })
                ->with(['danhMuc', 'chiTiet', 'images'])
                ->limit(3)
                ->get();

            return view('trangchu.property-detail', compact('property', 'relatedProperties'));
        } catch (\Exception $e) {
            \Log::error('Error in propertyDetail: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Không tìm thấy bất động sản này');
        }
    }
}
