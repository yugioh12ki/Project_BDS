<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'propertyvideos'; // Tên bảng trong cơ sở dữ liệu
    protected $primaryKey = 'VideoID'; // Khóa chính của bảng
    
    public $timestamps = false; // Không sử dụng timestamps
    
    protected $fillable = [
        'PropertyID', // Liên kết với PropertyID trong bảng properties
        'VideoPath',
        'Description',
        'UploadedDate'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }
}
