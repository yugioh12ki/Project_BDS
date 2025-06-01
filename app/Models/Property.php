<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties'; // Chỉ định tên bảng
    protected $primaryKey = 'PropertyID'; // Chỉ định khóa chính

    public $timestamps = false; // Bảng không có created_at và updated_at
    public $incrementing = false; // PropertyID là string, không auto-increment
    protected $keyType = 'string'; // PropertyID là string
    public function danhMuc()
    {
        return $this->belongsTo(DanhMucBDS::class, 'PropertyType', 'Protype_ID');
    }

    public function chiTiet()
    {
        return $this->hasOne(DetailProperty::class, 'PropertyID', 'PropertyID');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }
    public function agent()
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

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'PropertyID', 'PropertyID');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'ApprovedBy', 'UserID');
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
        'IdDetail',
        'TypePro',
    ];
}
