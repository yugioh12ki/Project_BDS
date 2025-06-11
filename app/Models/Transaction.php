<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $primaryKey = 'TransactionID';
    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    public function detailTransaction()
    {
        return $this->hasMany(detail_transaction::class, 'TransactionID', 'TransactionID');
    }

    public function document()
    {
        return $this->hasMany(Document::class, 'TransactionID', 'TransactionID');
    }
    public function transactionLog()
    {
        return $this->hasMany(transactionlog::class, 'TransactionID', 'TransactionID');
    }
    public function trans_owner()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }
    public function trans_agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }
    public function trans_cus()
    {
        return $this->belongsTo(User::class, 'CusID', 'UserID');
    }

    public function trans_property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

    // Alias for compatibility with existing code
    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

    public function trans_commission()
    {
        return $this->hasMany(Commission::class, 'TransactionID', 'TransactionID');
    }



    protected $fillable = [
        'TransactionID',
        'PropertyID',
        'OwnerID',
        'AgentID',
        'CusID',
        'TranStatus',
        'TotalPrice',
        'TransactionDate',
        'TransactionType',

    ];
}
