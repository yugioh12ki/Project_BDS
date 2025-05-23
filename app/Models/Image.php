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
        'ImagePath',
        'ImageURL',
        'Caption',
        'Description',
        'IsThumbnail',
        'UploadedDate'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }
}
