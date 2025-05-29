<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class feedback extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';
    protected $primaryKey = 'FeedbackID';
    public $timestamps = false;

    public function user_Cus()
    {
        return $this->belongsTo(profile_customer::class, 'CusID', 'UserID')->with('user');
    }

    public function user_Agent()
    {
        return $this->belongsTo(profile_agent::class, 'AgentID', 'UserID')->with('user');
    }

    public function customer()
    {
        return $this->belongsTo(profile_customer::class, 'CusID', 'UserID');
    }

    public function agent()
    {
        return $this->belongsTo(profile_agent::class, 'AgentID', 'UserID');
    }

    // Quan hệ trực tiếp đến bảng user cho customer và agent
    public function agent_user()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function customer_user()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }

}
