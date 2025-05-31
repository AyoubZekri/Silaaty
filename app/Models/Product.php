<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'categorie_id',
        'user_id',
        'product_name',
        'product_description',
        'product_quantity',
        'product_price',
        'product_price_total',
        'product_debtor_Name',
        'product_payment',
        'product_debtor_phone',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(categories::class, 'categorie_id');
    }
}

