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
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
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
    function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function favoriteProperties()
    {
        // نحدد اسم الجدول الوسيط يدوياً لأنه يحوي اسمين مختلفين
        return $this->belongsToMany(Property::class, 'property_user')->withTimestamps();
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
