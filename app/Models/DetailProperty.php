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

}
