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
        'Name',
        'Email',
        'Birth',
        'Sex',
        'IdentityCard',
        'Phone',
        'Address',
        'Ward',
        'District',
        'Province',
        'Role',
        'StatusUser',
        'PasswordHash',
    ];

    // Mối quan hệ với bảng 'Property'

    public function chusohuu()
    {
        return $this->hasMany(Property::class, 'OwnerID', 'UserID');
    }

    public function moigioi()
    {
        return $this->hasMany(Property::class, 'AgentID', 'UserID');
    }

    public function quantri()
    {
        return $this->hasMany(Property::class, 'ApprovedBy', 'UserID');
    }

    // Mối quan hệ với bảng 'appointment'

    public function appoint_agent()
    {
        return $this->hasMany(Appointment::class, 'AgentID', 'UserID');
    }
    public function appoint_owner()
    {
        return $this->hasMany(Appointment::class, 'OwnerID', 'UserID');
    }
    public function appoint_customer()
    {
        return $this->hasMany(Appointment::class, 'CusID', 'UserID');
    }

    // Mối quan hệ với bảng 'commission'

    // Mối quan hệ với bảng transaction
    public function trans_owner()
    {
        return $this->hasMany(Transaction::class, 'OwnerID', 'UserID');
    }
    public function trans_agent()
    {
        return $this->hasMany(Transaction::class, 'AgentID', 'UserID');
    }

    public function trans_customer()
    {
        return $this->hasMany(Transaction::class, 'CusID', 'UserID');
    }

    // Mối quan hệ với bảng 'feedback'
    public function feedback_agent()
    {
        return $this->hasMany(Feedback::class, 'AgentID', 'UserID');
    }

    public function feedback_customer()
    {
        return $this->hasMany(Feedback::class, 'CusID', 'UserID');
    }

    // Mối quan hệ với bảng 'profile_*' của bảng

    public function profile_agent()
    {
        return $this->hasOne(profile_agent::class, 'UserID', 'UserID');
    }

    public function comm_agent()
    {
        return $this->hasOne(Commission::class, 'UserID', 'UserID');
    }


    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['PasswordHash'] = bcrypt($password);
    }

    public function isActive()
    {
        return strtolower($this->StatusUser) === 'active'; // So sánh không phân biệt chữ hoa/thường
    }

    /**
     * Get the number of active properties assigned to this agent.
     *
     * @return int
     */
    public function getActivePropertyCountAttribute()
    {
        if (strtolower($this->Role) !== 'agent') {
            return 0;
        }

        return Property::where('AgentID', $this->UserID)
                      ->where('Status', 'active')
                      ->count();
    }

}
