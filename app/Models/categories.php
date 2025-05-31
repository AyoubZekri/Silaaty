<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_name',
        'categories _image',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

