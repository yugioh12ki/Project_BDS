<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';
    protected $primaryKey = 'AppointmentID';

    public $timestamps = false;

    protected $fillable = [
        'AppointmentID',
        'CusID',           // Thay đổi từ UserID thành CusID theo CSDL
        'PropertyID',
        'AgentID',
        'AppointmentDateStart',
        'AppointmentDateEnd',
        'Status',
        'Description' 
    ];

    //Mối quan hệ với bảng 'user'
    public function user_agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');  // Sửa lại relationship
    }

    //Mối quan hệ với bảng 'property'

    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

}
