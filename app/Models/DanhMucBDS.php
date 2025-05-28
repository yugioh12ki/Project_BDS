<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucBDS extends Model
{
    use HasFactory;
    protected $table = 'danhmuc_pro'; // Tên bảng trong cơ sở dữ liệu
    protected $primaryKey = 'Protype_ID'; //

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    public function properties()
    {
        return $this->hasMany(Property::class, 'PropertyType', 'Protype_ID'); // Sửa tên khóa ngoại phù hợp với model Property
    }

    protected $fillable = [
        'Protype_ID',
        'ten_pro'
    ];
}
