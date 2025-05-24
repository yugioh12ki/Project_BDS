<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\DanhMucBDS;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách danh mục
        $danhmucs = DanhMucBDS::all();
        
        // Lấy BĐS mới nhất (active)
        $recentProperties = Property::with(['danhMuc', 'chiTiet'])
            ->where('Status', 'active')
            ->orderBy('PostedDate', 'desc')
            ->limit(4)
            ->get();
        
        // Lấy BĐS nổi bật - giá cao nhất (active)
        $featuredProperties = Property::with(['danhMuc', 'chiTiet'])
            ->where('Status', 'active')
            ->orderBy('Price', 'desc')
            ->limit(3)
            ->get();

        return view('trangchu.index', compact('danhmucs', 'recentProperties', 'featuredProperties'));
    }
}
