<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class invoies extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "Transaction_id",
        "invoies_numper",
        'invoies_date',
        'invoies_payment_date',
        'invoies_status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'Transaction_id');
    }
}

