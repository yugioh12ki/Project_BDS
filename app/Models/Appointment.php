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

}
