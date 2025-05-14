<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $primaryKey = 'DocumentID';

    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }
}
