<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profile_admin extends Model
{
    use HasFactory;

    protected $table = 'profile_admin';
    protected $primaryKey = 'userID';
    public $incrementing = false;
    public $timestamps = false;

    public $keyType = 'string';



}
