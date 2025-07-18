<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        "family_name",
        "phone_number",
        'email',
        'password',
        'google_id',
        'email_verified',
        "profile_image",
        'user_notify_status',
        'fcm_token',
        'user_role',
        'Status',
        "created_at",
        "date_experiment",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function user_roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'roles_user', 'user_id', 'roles_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function zakat()
    {
        return $this->hasOne(Zakat::class);
    }


    public function notifications()
    {

        return $this->hasMany('App\Models\Notifications');
    }

    public function isAdmin()
    {
        return $this->user_roles()->where('role_name', 'admin')->exists();
    }

    public function isUser()
    {
        return $this->user_roles()->where('role_name', 'User')->exists();
    }

    public function isDealer()
    {
        return $this->user_roles()->where('role_name', 'Dealer')->exists();
    }
    public function isConvicts()
    {
        return $this->user_roles()->where('role_name', 'Convicts')->exists();
    }

}
