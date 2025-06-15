<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoris extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "categoris_name",
        "categoris_image"
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
