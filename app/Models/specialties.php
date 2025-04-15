<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class specialties extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_fr',
        'specialy_img',
    ];

    /**
     * Get the doctors that have this specialty.
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'specialties_id');
    }
}
