<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'price',
        'description',
        'user_id',
        'is_delete',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
