<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions'; // Chỉ định tên bảng là 'user' nếu không phải 'users'

    protected $primaryKey = 'TransactionID'; // Chỉ định khóa chính là 'id'
    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    public $incrementing = false; // Nếu khóa chính không phải là số nguyên tự động tăng

    protected $keyType = 'string'; // Nếu khóa chính là chuỗi

    protected $fillable = [
        'PropertyID',
        'OwnerID',
        'CustomerID',
        'AgentID',
        'TransactionType',
        'Amount',
        'Commission',
        'TransactionDate',
        'Status',
        'Description',
        'PaymentMethod'
    ];

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

    /**
     * Get the property associated with the transaction.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'PropertyID', 'PropertyID');
    }

    /**
     * Get the owner associated with the transaction.
     */
    public function trans_owner()
    {
        return $this->belongsTo(User::class, 'OwnerID', 'UserID');
    }

    /**
     * Get the customer associated with the transaction.
     */
    public function trans_cus()
    {
        return $this->belongsTo(User::class, 'CustomerID', 'UserID');
    }

    /**
     * Get the agent associated with the transaction.
     */
    public function trans_agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }
}
