<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profile_customer extends Model
{
    use HasFactory;

    protected $table = 'profile_customer';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';

    protected $fillable = [
        'UserID',
        'Whitelist',
        'PreferredPropertyType'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
