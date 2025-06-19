<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commission'; // Chỉ định tên bảng là 'user' nếu không phải 'users'
    protected $primaryKey = 'CommissionID'; // Chỉ định khóa chính là 'id'

    public $incrementing = true;
    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    protected $keyType = 'int';

    public function comm_agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }

    public function comm_trans()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }

    protected $fillable = [
        'CommissionID',
        'TransactionID',
        'AgentID',
        'Amount',
        'Percentage', // Phần trăm hoa hồng
        'TypeCom', // Loại hoa hồng (ví dụ: 'sale', 'rent')
        'PaidDate', // Ngày thanh toán hoa hồng
        'StatusCommission', // Trạng thái hoa hồng (ví dụ: 'pending', 'success', 'Cancelled')
    ];

    protected $casts = [
        'PaidDate' => 'datetime',
    ];



    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'TransactionID', 'TransactionID');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'AgentID', 'UserID');
    }
}
