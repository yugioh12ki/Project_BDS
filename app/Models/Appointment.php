<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';
    protected $primaryKey = 'AppointmentID';

    public $timestamps = false;

    // Fillable attributes
    protected $fillable = [
        'PropertyID', 'AgentID', 'CusID', 'OwnerID',
        'TitleAppoint', 'DescAppoint', 'AppointmentDateStart', 
        'AppointmentDateEnd', 'Status'
    ];

    // Cast attributes
    protected $casts = [
        'AppointmentDateStart' => 'datetime',
        'AppointmentDateEnd' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'Đã Hủy';
    const STATUS_COMPLETED = 'Hoàn Thành';

    // Scopes
    public function scopePending($query)
    {
        return $query->where('Status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('Status', self::STATUS_CONFIRMED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('AppointmentDateStart', Carbon::today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('AppointmentDateStart', '>=', Carbon::today())
                     ->where('Status', '!=', self::STATUS_CANCELLED);
    }

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
        return $this->belongsTo(User::class, 'CustomerID', 'UserID');
    }

    //Mối quan hệ với bảng 'property'

    public function property()
    {
        return $this->belongsTo(Property::class, 'ProID', 'PropertyID');
        // Thay 'ProID' bằng tên cột thật trong bảng appointments của bạn
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'CustomerID', 'UserID');
    }

    // Quan hệ với người dùng (Chủ nhà)
    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }

    // Quan hệ với người dùng (Khách xem nhà)
    public function cusUser()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }

    public function khachHang() {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }
}
