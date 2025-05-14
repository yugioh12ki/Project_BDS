<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transactionlog extends Model
{
    use HasFactory;

    protected $table = 'transactionlog';
    protected $primaryKey = 'LogID';

    public $timestamps = false;

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }
    
}
