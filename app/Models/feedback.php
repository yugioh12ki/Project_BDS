<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks'; // Chỉ định tên bảng là 'user' nếu không phải 'users'

    protected $primaryKey = 'FeedbackID'; // Chỉ định khóa chính là 'id'
    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at


}
