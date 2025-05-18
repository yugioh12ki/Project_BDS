<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'propertyimage'; // Tên bảng trong cơ sở dữ liệu
    protected $primaryKey = 'ImageID'; // Khóa chính của bảng
    
    public $timestamps = false; // Không sử dụng timestamps

    protected $fillable = [
        'PropertyID', // Liên kết với PropertyID trong bảng properties
        'ImagePath',
        'Caption',
        'UploadedDate'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }
}
