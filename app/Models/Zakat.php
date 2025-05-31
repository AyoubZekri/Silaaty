<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zakat extends Model
{
    use HasFactory;

    protected $table = 'zakats';

    protected $fillable = [
        'user_id',
        'zakat_nisab',
        'zakat_total_asset_value',
        'zakat_due_amount',
        'zakat_due',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

