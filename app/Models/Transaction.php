<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions'; // Chỉ định tên bảng là 'user' nếu không phải 'users'

    protected $primaryKey = 'TransactionID'; // Chỉ định khóa chính là 'id'
    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    public $incrementing = false; // Nếu khóa chính không phải là số nguyên tự động tăng

    protected $keyType = 'string'; // Nếu khóa chính là chuỗi
}
