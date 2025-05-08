<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'user'; // Chỉ định tên bảng là 'user' nếu không phải 'users'
    protected $primaryKey = 'UserID'; // Chỉ định khóa chính là 'id'

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    public $incrementing = false; // Nếu khóa chính không phải là số nguyên tự động tăng

    protected $keyType = 'string'; // Nếu khóa chính là chuỗi

    protected $fillable = [
        'Email','PasswordHash','Role','StatusUser','Name'
    ];

    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function isActive()
    {
        return strtolower($this->StatusUser) === 'active'; // So sánh không phân biệt chữ hoa/thường
    }
}
