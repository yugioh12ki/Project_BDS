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
    public $incrementing = true; // Đảm bảo primary key là auto increment
    protected $keyType = 'int'; // Đảm bảo primary key là integer

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
    const STATUS_PENDING = 'Khởi Tạo';
    const STATUS_ACTIVE = 'Đang Thực Hiện';
    const STATUS_CANCELLED = 'Hủy Hẹn';
    const STATUS_COMPLETED = 'Hoàn Thành';

    // Scopes
    public function scopePending($query)
    {
        return $query->where('Status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('Status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('Status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('Status', self::STATUS_CANCELLED);
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

    public function agentUser()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }

    public function cusUser()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

}
