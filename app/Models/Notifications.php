<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'title',
        'uuid',
        'content',
        'is_read',
        'user_id',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    // public function getUserName()
    // {
    //     $user = $this->user;
    //     return $user->role == 2 ? $user->normalUser->name : $user->pharmUser->name;
    // }
}
