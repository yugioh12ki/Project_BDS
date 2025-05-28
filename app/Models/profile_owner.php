<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profile_owner extends Model
{
    use HasFactory;
    protected $table = 'profile_owner';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';

    protected $fillable = [
        'UserID',
        'ContactOwner',
        'NumberCardOwner',
        'GiayTo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
