<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_code', 'user_id', 'kost_id', 'room_id',
        'check_in_date', 'check_out_date', 'duration_months',
        'total_price', 'deposit', 'payment_status', 'booking_status',
        'payment_method', 'payment_proof', 'notes', 'special_requests',
        'paid_at', 'confirmed_at'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'total_price' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'BK-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->booking_status) {
            'pending' => '<span class="badge bg-warning">Menunggu</span>',
            'confirmed' => '<span class="badge bg-info">Dikonfirmasi</span>',
            'active' => '<span class="badge bg-success">Aktif</span>',
            'completed' => '<span class="badge bg-secondary">Selesai</span>',
            'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>',
            default => '<span class="badge bg-light">-</span>',
        };
    }
}
