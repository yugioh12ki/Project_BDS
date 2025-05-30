<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\DanhMucBDS;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách danh mục
        $danhmucs = DanhMucBDS::all();

        // Lấy BĐS Bán nổi bật (4 properties with TypePro = 'Sale')
        $saleProperties = Property::with(['danhMuc', 'chiTiet'])
            ->where('Status', 'active')
            ->where('TypePro', 'Sale')
            ->orderBy('Price', 'desc')
            ->limit(4)
            ->get();

        // Lấy BĐS Thuê nổi bật (4 properties with TypePro = 'Rent')
        $rentProperties = Property::with(['danhMuc', 'chiTiet'])
            ->where('Status', 'active')
            ->where('TypePro', 'Rent')
            ->orderBy('Price', 'desc')
            ->limit(4)
            ->get();

        return view('trangchu.index', compact('danhmucs', 'saleProperties', 'rentProperties'));
    }

    public function getUser($id)
    {
        return response()->json(User::find($id));
    }
}
