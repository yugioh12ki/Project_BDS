<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class feedback extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';
    protected $primaryKey = 'FeedbackID';
    public $incrementing = true;
    public $timestamps = false;

    public function user_Cus()
    {
        return $this->belongsTo(User::class, 'CustomerID', 'UserID');
    }

    public function user_Agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }
}
