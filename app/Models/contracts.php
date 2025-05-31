<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contracts extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';

    protected $primaryKey = 'ContractID';

    public function trans_contract()
    {
        return $this->belongsTo(Property::class, 'TransactionID', 'TransactionID');
    }

    protected $fillable = [
        'ContractID',
        'TransactionID',
        'SignedDate',
        'Detals'
    ];
}
