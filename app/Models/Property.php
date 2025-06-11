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

    public function danh_muc() // Thêm alias hỗ trợ tên relationship trong JSON
    {
        return $this->belongsTo(DanhMucBDS::class, 'PropertyType', 'Protype_ID');
    }

    public function chiTiet()
    {
        return $this->hasOne(DetailProperty::class, 'PropertyID', 'PropertyID');
    }

    public function chi_tiet() // Thêm alias hỗ trợ tên relationship trong JSON
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

    // public function moigioi()
    // public function owner()
    // {
    //     return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    // }
    // public function agent()
    // {
    //     return $this->belongsTo(User::class, 'AgentID', 'UserID');
    // }
    public function quantri()
    {
        return $this->belongsTo(User::class, 'ApprovedBy', 'UserID');
    }

    // === NEW RELATIONSHIPS - KHÔNG ẢNH HƯỞNG CODE CŨ ===

    // Relationships mới với tên rõ ràng (dành cho code mới)
    public function owner()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'ApprovedBy', 'UserID');
    }

    // Relationship mới để lấy thông tin profile_agent của agent
    public function agent_profile()
    {
        return $this->hasOneThrough(
            profile_agent::class,
            User::class,
            'UserID',      // FK on User table
            'UserID',      // FK on profile_agent table
            'AgentID',     // Local key on Property table
            'UserID'       // Local key on User table
        );
    }

    // Alternative: Direct relationship to profile_agent
    public function agent_profile_direct()
    {
        return $this->belongsTo(profile_agent::class, 'AgentID', 'UserID');
    }

    // Thêm mới quan hệ với video
    public function videos()
    {
        return $this->hasMany(Video::class, 'PropertyID', 'PropertyID');
    }

    // Thêm quan hệ với ảnh
    public function images()
    {
        return $this->hasMany(Image::class, 'PropertyID', 'PropertyID');
    }



    // public function videos()
    // {
    //     return $this->hasMany(Video::class, 'PropertyID', 'PropertyID');
    // }

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
         'UserCreated',
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
