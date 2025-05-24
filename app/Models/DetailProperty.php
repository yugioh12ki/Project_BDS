<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProperty extends Model
{
    use HasFactory;

    protected $table = 'detail_pro'; // Tên bảng trong cơ sở dữ liệu
    protected $primaryKey = 'IdDetail'; // Khóa chính của bảng
    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    protected $fillable = [
        'IdDetail', 'Floor', 'Area', 'Bedroom', 'Bath_WC', 'Road', 'legal', 'Interior', 'WaterPrice', 'PowerPrice', 'Utilities'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'IdDetail', 'IdDetail'); // Khóa ngoại trong bảng detail_pro
    }

    // Diện tích hiển thị
    public function getDienTichText()
    {
        return $this->Area ? $this->Area . ' m²' : 'N/A m²';
    }

    // Số phòng ngủ
    public function getSoPhongNguText()
    {
        return $this->Bedroom ? $this->Bedroom : 'N/A';
    }

    // Số phòng tắm/WC
    public function getSoPhongTamWCText()
    {
        return $this->Bath_WC ? $this->Bath_WC : 'N/A';
    }

    // Số tầng
    public function getSoTangText()
    {
        return $this->Floor ? $this->Floor : 'N/A';
    }

    // Đường rộng
    public function getDuongRongText()
    {
        return $this->Road ? $this->Road . 'm' : 'N/A';
    }

    // Pháp lý
    public function getPhapLyText()
    {
        return $this->legal ?: 'N/A';
    }

    // Nội thất
    public function getNoiThatText()
    {
        return $this->Interior ?: 'N/A';
    }

    // Giá điện
    public function getGiaDienText()
    {
        // Cast to float before formatting
        $price = floatval($this->PowerPrice);
        return $price > 0 ? number_format($price, 0, ',', '.') . ' đ/kWh' : '0 đ/kWh';
    }

    // Giá nước
    public function getGiaNuocText()
    {
        // Cast to float before formatting
        $price = floatval($this->WaterPrice);
        return $price > 0 ? number_format($price, 0, ',', '.') . ' đ/m³' : '0 đ/m³';
    }

    // Tiện ích
    public function getTienIchText()
    {
        return $this->Utilities ?: 'Không có thông tin';
    }
}
