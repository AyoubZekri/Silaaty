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
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

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
