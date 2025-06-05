<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $primaryKey = 'DocumentID';

    public $incrementing = true; // DocumentID is auto-increment
    public $timestamps = false;
    public $keyType = 'int'; // DocumentID is integer

    protected $fillable = [
        'TransactionID',
        'UploadedDate', // Match database column name
        'FilePath', // Match database column name
        'DocumentType'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }
}
