<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerStock extends Model
{
    protected $table = 'seller_stocks';

    protected $fillable = [
        'uuid',
        'user_id',
        'seller_id',
        'product_id',
        'quantity',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
