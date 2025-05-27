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
        'PropertyID',
        'AgentID',
        'OwnerID',
        'CusID',
        'TitleAppoint',
        'DescAppoint',
        'AppointmentDateStart',
        'AppointmentDateEnd',
        'Status'
    ];

    //Mối quan hệ với bảng 'user'
    public function agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }

    //Mối quan hệ với bảng 'property'
    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

}
