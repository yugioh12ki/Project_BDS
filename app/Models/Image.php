<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'propertyimage';

    protected $primaryKey = 'ImageID';
    public $timestamps = false;
    
    protected $fillable = [
        'ImageID',
        'PropertyID',
        'ImageURL',
        'Description',
        'IsThumbnail'
    ];
    
    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }
}
