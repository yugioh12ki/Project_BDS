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
    public $incrementing = true; // Nếu khóa chính không phải là số nguyên tự động tăng

    protected $keyType = 'int'; // Nếu khóa chính là số nguyên
    protected $fillable = [
        'IdDetail', 'PropertyID', 'Levelhouse', 'Floor', 'HouseLength', 'HouseWidth', 'TotalLength', 'TotalWidth', 'Bedroom', 'Balcony', 'Bath_WC', 'Road', 'legal', 'view', 'near', 'Interior', 'WaterPrice', 'PowerPrice', 'Utilities'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID'); // Khóa ngoại PropertyID trong bảng detail_pro
    }

    // Accessor methods for better display
    public function getDienTichText()
    {
        // Calculate area from available fields
        if ($this->TotalLength && $this->TotalWidth) {
            $area = $this->TotalLength * $this->TotalWidth;
            return $area . ' m²';
        } elseif ($this->HouseLength && $this->HouseWidth) {
            $area = $this->HouseLength * $this->HouseWidth;
            return $area . ' m²';
        }
        return 'N/A';
    }

    public function getSoPhongNguText()
    {
        return $this->Bedroom ? $this->Bedroom . ' phòng' : 'N/A';
    }

    public function getSoPhongTamWCText()
    {
        return $this->Bath_WC ? $this->Bath_WC . ' phòng' : 'N/A';
    }

    public function getSoTangText()
    {
        return $this->Floor ? $this->Floor . ' tầng' : 'N/A';
    }

    public function getDuongRongText()
    {
        return $this->Road ? $this->Road . ' m' : 'N/A';
    }

    public function getPhapLyText()
    {
        return $this->legal ? $this->legal : 'N/A';
    }

    public function getNoiThatText()
    {
        return $this->Interior ? $this->Interior : 'N/A';
    }

    public function getGiaDienText()
    {
        return $this->PowerPrice ? $this->PowerPrice : 'N/A';
    }

    public function getGiaNuocText()
    {
        return $this->WaterPrice ? $this->WaterPrice : 'N/A';
    }

    // Computed property for Area
    public function getAreaAttribute()
    {
        if ($this->TotalLength && $this->TotalWidth) {
            return $this->TotalLength * $this->TotalWidth;
        } elseif ($this->HouseLength && $this->HouseWidth) {
            return $this->HouseLength * $this->HouseWidth;
        }
        return 0;
    }


}
