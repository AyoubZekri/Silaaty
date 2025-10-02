<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'transactions', // 1 مورد
        'name',           // 2 زبون
        'family_name',
        'phone_number',
        "Status",
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
