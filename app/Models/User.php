<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'avatar', 'nik', 'gender', 'address', 'dob',
        'occupation', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dob' => 'date',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getAvatarUrlAttribute()
{
    if ($this->avatar) {
        // Kita ambil ID Proyek Anda langsung dari URL asli Supabase Anda
        $projectId = 'itkwzhuxntqnisniesrd';

        // Memaksa URL mengarah ke jalur Public Object milik Supabase, bukan S3 signature
        return "https://{$projectId}.storage.supabase.co/storage/v1/object/public/kostQu/{$this->avatar}";
    }

    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
}
}
