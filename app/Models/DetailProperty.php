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

}
