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

    // Fillable fields - exclude fields that triggers handle automatically
    protected $fillable = [
        'PropertyID',
        'CusID',
        'TotalPrice',
        'TransactionDate',
        'TranStatus'
        // Don't include: TransactionID, AgentID, OwnerID, TransactionType (triggers handle these)
        // Don't include: Description (field doesn't exist in database)
    ];

    public function detailTransaction()
    {
        return $this->hasMany(detail_transaction::class, 'TransactionID', 'TransactionID');
    }

    public function document()
    {
        return $this->hasMany(Document::class, 'TransactionID', 'TransactionID');
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



    // protected $fillable = [
    //     // Don't include TransactionID - trigger auto-generates
    //     'PropertyID',
    //     // Don't include OwnerID, AgentID, TransactionType - trigger gets from properties table
    //     'CusID',
    //     'TranStatus',
    //     'TotalPrice',
    //     'TransactionDate',
    //     'Description',
    // ];

    protected $casts = [
        'TransactionDate' => 'datetime',
    ];
}
