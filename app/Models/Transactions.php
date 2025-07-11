<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transactions',
        'name',
        'family_name',
        'phone_number',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
