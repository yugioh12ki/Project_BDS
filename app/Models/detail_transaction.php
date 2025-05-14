<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detail_transaction extends Model
{
    use HasFactory;

    protected $table = 'detail_transaction';
    protected $primaryKey = ['TransactionID', 'Num_Pay'];
    public $timestamps = false;
    public $incrementing = false;

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }
}
