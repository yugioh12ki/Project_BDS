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
        
        return view('trangchu.index', compact('danhmucs'));
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
        // Tạo query builder cho model Property
        $query = Property::query()
            ->with(['danhMuc', 'chiTiet'])
            ->where('Status', 1); // Chỉ lấy BĐS đã được duyệt

        // Tìm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('Title', 'like', '%'.$keyword.'%')
                  ->orWhere('Description', 'like', '%'.$keyword.'%')
                  ->orWhere('Province', 'like', '%'.$keyword.'%')
                  ->orWhere('District', 'like', '%'.$keyword.'%')
                  ->orWhere('Ward', 'like', '%'.$keyword.'%')
                  ->orWhere('Address', 'like', '%'.$keyword.'%');
            });
        }

        // Tìm theo loại hình BĐS
        if ($request->filled('type')) {
            $query->where('PropertyType', $request->type);
        }

        // Tìm theo khoảng giá
        if ($request->filled('price')) {
            switch ($request->price) {
                case 1: // Dưới 1 tỷ
                    $query->where('Price', '<', 1000000000);
                    break;
                case 2: // 1-3 tỷ
                    $query->whereBetween('Price', [1000000000, 3000000000]);
                    break;
                case 3: // 3-5 tỷ
                    $query->whereBetween('Price', [3000000000, 5000000000]);
                    break;
                case 4: // Trên 5 tỷ
                    $query->where('Price', '>', 5000000000);
                    break;
            }
        }

        // Tìm theo diện tích (thông qua bảng detail_pro)
        if ($request->filled('area')) {
            $query->whereHas('chiTiet', function($q) use ($request) {
                switch ($request->area) {
                    case 1: // Dưới 30m²
                        $q->where('Area', '<', 30);
                        break;
                    case 2: // 30-50m²
                        $q->whereBetween('Area', [30, 50]);
                        break;
                    case 3: // 50-80m²
                        $q->whereBetween('Area', [50, 80]);
                        break;
                    case 4: // Trên 80m²
                        $q->where('Area', '>', 80);
                        break;
                }
            });
        }

        // Thực hiện query và lấy kết quả
        $properties = $query->paginate(12);
        
        // Lấy danh sách danh mục để hiển thị trong form tìm kiếm
        $danhmucs = DanhMucBDS::all();
        
        // Truyền dữ liệu ra view
        return view('trangchu.search-results', compact('properties', 'danhmucs'));
    }

    public function propertyDetail($id)
    {
        // Tìm property theo ID
        $property = Property::with(['danhMuc', 'chiTiet', 'chusohuu', 'moigioi'])
            ->where('PropertyID', $id)
            ->where('Status', 1) // Chỉ lấy BĐS đã được duyệt
            ->firstOrFail();
        
        // Lấy các BĐS liên quan (cùng loại, cùng khu vực)
        $relatedProperties = Property::where('Status', 1)
            ->where('PropertyID', '!=', $id)
            ->where(function($query) use ($property) {
                $query->where('PropertyType', $property->PropertyType)
                    ->orWhere('District', $property->District);
            })
            ->with(['danhMuc', 'chiTiet'])
            ->limit(3)
            ->get();
        
        return view('trangchu.property-detail', [
            'property' => $property,
            'relatedProperties' => $relatedProperties,
        ]);
    }
}
