<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $primaryKey = 'DocumentID';

    public $incrementing = true;
    public $timestamps = false;


    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }

    // Add relationship alias for customer security checks
    public function doc_transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }

    protected $fillable = [
        'DocumentID',
        'TransactionID',
        'UploadedDate',
        'DocumentType',
        'DocumentName',
        'FilePath'
    ];
}
