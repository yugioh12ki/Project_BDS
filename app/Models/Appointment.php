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
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }
}
