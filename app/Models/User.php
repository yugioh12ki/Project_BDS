<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user'; // Chỉ định tên bảng là 'user' nếu không phải 'users'
    protected $primaryKey = 'UserID'; // Chỉ định khóa chính là 'UserID'

    public $timestamps = false; // Bảng không có created_at và updated_at
    public $incrementing = false; // UserID là string không auto-increment
    protected $keyType = 'string'; // UserID là string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->UserID)) {
                $user->UserID = 'USER_' . uniqid() . '_' . time();
            }
        });
    }

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
        'Avatar',
        'otp',
        'otp_expires_at', // Đổi từ otp_expired_at thành otp_expires_at để khớp với SQL
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

    // === NEW RELATIONSHIPS - KHÔNG ẢNH HƯỞNG CODE CŨ ===

    // Relationships mới với tên rõ ràng hơn (dành cho code mới)
    public function owned_properties()
    {
        return $this->hasMany(Property::class, 'OwnerID', 'UserID');
    }

    public function managed_properties()
    {
        return $this->hasMany(Property::class, 'AgentID', 'UserID');
    }

    public function approved_properties()
    {
        return $this->hasMany(Property::class, 'ApprovedBy', 'UserID');
    }

    // Relationship mới để lấy properties với agent profile information
    public function agent_properties_with_profile()
    {
        return $this->managed_properties()->with(['agent_profile']);
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
    // Direct relationship for agent feedbacks (for performance)
    public function agent_feedbacks()
    {
        return $this->hasMany(feedback::class, 'AgentID', 'UserID');
    }

    // Direct relationship for customer feedbacks (for performance)
    public function customer_feedbacks()
    {
        return $this->hasMany(feedback::class, 'CusID', 'UserID');
    }

    public function feedback_agent()
    {
        return $this->hasManyThrough(
            feedback::class,
            profile_agent::class,
            'UserID', // Khóa ngoại trên bảng profile_agent
            'AgentID', // Khóa ngoại trên bảng feedback
            'UserID', // Khóa chính trên bảng user
            'UserID' // Khóa chính trên bảng profile_agent
        );
    }

    public function feedback_customer()
    {
        return $this->hasManyThrough(
            feedback::class,
            profile_customer::class,
            'UserID', // Khóa ngoại trên bảng profile_customer
            'CusID', // Khóa ngoại trên bảng feedback
            'UserID', // Khóa chính trên bảng user
            'UserID' // Khóa chính trên bảng profile_customer
        );
    }

    // Mối quan hệ với bảng 'profile_*' của bảng

    public function profile_admin()
    {
        return $this->hasOne(profile_admin::class, 'UserID', 'UserID');
    }

    public function profile_agent()
    {
        return $this->hasOne(profile_agent::class, 'UserID', 'UserID');
    }

    public function profile_customer()
    {
        return $this->hasOne(profile_customer::class, 'UserID', 'UserID');
    }
    public function profile_owner()
    {
        return $this->hasOne(profile_owner::class, 'UserID', 'UserID');
    }

    public function comm_agent()
    {
        return $this->hasOne(Commission::class, 'UserID', 'UserID');
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
        $this->attributes['PasswordHash'] = md5($password);
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

    /**
     * Get total approved feedback count for agent
     */
    public function getTotalRatingsAttribute()
    {
        if ($this->Role !== 'Agent') {
            return 0;
        }

        return $this->agent_feedbacks()->where('Status', 'Đã duyệt')->count();
    }

    /**
     * Get average rating for agent
     */
    public function getAverageRatingAttribute()
    {
        if ($this->Role !== 'Agent') {
            return 0;
        }

        $average = $this->agent_feedbacks()
            ->where('Status', 'Đã duyệt')
            ->avg('Rating');

        return $average ? round($average, 1) : 0;
    }


}
