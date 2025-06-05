<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments'; // Chỉ định tên bảng là 'user' nếu không phải 'users'
    protected $primaryKey = 'AppointmentID'; // Chỉ định khóa chính là 'id'

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    //Mối quan hệ với bảng 'user'
    public function user_owner()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }
    public function user_agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function user_customer()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }

    //Mối quan hệ với bảng 'property'
    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

    protected $fillable = [
        'AppointmentID',
        'PropertyID',
        'OwnerID',
        'AgentID',
        'CusID',
        'AppointmentDate',
        'Status', // Trạng thái cuộc hẹn (ví dụ: 'pending', 'confirmed', 'cancelled')
        'Notes', // Ghi chú về cuộc hẹn
    ];
}
