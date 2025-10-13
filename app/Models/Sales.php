<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'invoie_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoies::class, 'invoie_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


}
