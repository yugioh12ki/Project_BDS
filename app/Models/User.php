<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user'; // Tên bảng là 'user'
    protected $primaryKey = 'UserID'; // Khóa chính là 'UserID'

    public $timestamps = false; // Bảng không có created_at và updated_at
    public $incrementing = false; // UserID là string không auto-increment
    protected $keyType = 'string'; // UserID là string

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
    
    public function properties()
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
        return $this->hasMany(Feedback::class, 'OwnerID', 'UserID');
    }



    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function getAuthIdentifier()
    {
        return $this->UserID;
    }

    public function getAuthIdentifierName()
    {
        return 'UserID';
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['PasswordHash'] = bcrypt($password);
    }

    public function isActive()
    {
        return strtolower($this->StatusUser) === 'active'; // So sánh không phân biệt chữ hoa/thường
    }
}
