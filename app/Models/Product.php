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
        "invoies_id",
        'product_name',
        "Product_image",
        'product_description',
        'product_quantity',
        'product_price',
        'product_price_total',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(invoies::class, 'invoies_id');
    }
}

