<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profile_agent extends Model
{
    use HasFactory;
    
    protected $table = 'profile_agent';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'UserID',
        'Certificate',
        'DistrictAgent',
        'ProvinceAgent',
        'ContactAgent',
        'NumberCardAgent'
    ];
    
    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
