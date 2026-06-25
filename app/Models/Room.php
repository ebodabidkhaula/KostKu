<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'kost_id', 'room_number', 'floor', 'size',
        'price', 'status', 'description', 'facilities'
    ];

    protected $casts = [
        'facilities' => 'array',
        'price' => 'decimal:2',
    ];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
