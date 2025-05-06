<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'user'; // Chỉ định tên bảng là 'user' nếu không phải 'users'
    //protected $primaryKey = 'UserID'; // Chỉ định khóa chính là 'id'

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    protected $fillable = [
        'Email','PasswordHash','Role','Status'
    ];
}
