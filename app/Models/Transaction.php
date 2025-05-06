<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions'; // Chỉ định tên bảng là 'user' nếu không phải 'users'

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

}
