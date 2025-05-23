<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties'; // Chỉ định tên bảng là 'user' nếu không phải 'users'
    protected $primaryKey = 'PropertyID'; // Chỉ định khóa chính là 'id'

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at
    public $incrementing = false; // Nếu khóa chính không phải là số nguyên tự động tăng

    protected $keyType = 'string'; // Nếu khóa chính là chuỗi

    public function danhMuc()
    {
        return $this->belongsTo(DanhMucBDS::class, 'PropertyType', 'Protype_ID');
    }

    public function chiTiet()
    {
        return $this->hasOne(DetailProperty::class, 'PropertyID', 'PropertyID');
    }

    public function chusohuu()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }

    public function moigioi()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function quantri()
    {
        return $this->belongsTo(User::class, 'ApprovedBy', 'UserID');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'PropertyID', 'PropertyID');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'PropertyID', 'PropertyID');
    }

    protected $fillable = [
         'OwnerID',
         'AgentID',
         'PostedDate',
         'ApprovedBy',
         'ApprovedDate',
         'Status',
         'Province',
         'District',
         'Ward',
         'Address',
         'PropertyType',
          'Price',
          'Title',
        'Description',
        'TypePro',
        'ContactPhone',
        'ContactEmail',
    ];
}
