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

    public function detail_trans()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }

    protected $fillable = [
        'TransactionID',
        // Don't include Num_Pay - trigger auto-increments
        // Don't include DTran_Date - trigger sets to NOW()
        // For rent: don't include Price - trigger calculates from property.Price * RentMonth
        // For sale: Price is manually set
        'Price',
        'RentMonth',
        'InstallPayment',
        'PaymentType',
        'DTran_Status'
    ];


    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }
}
