<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'categorie_id',  // النوع 1 البضائع العادية 2البضائع الضرورية 4 الديون لك وعليك
        'categoris_id',  // الفئة يتم انشائها بواسطة المستخدم
        'user_id',
        "invoies_id",
        'product_name',
        "Product_image",
        'product_description',
        'product_quantity',
        'product_price',
        'product_price_purchase',
        'product_price_total',
        "product_price_total_purchase"
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

