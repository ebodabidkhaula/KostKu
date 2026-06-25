<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KostPhoto extends Model
{
    protected $fillable = [
        'kost_id', 'photo_path', 'caption', 'type', 'is_primary', 'order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('supabase')->url($this->photo_path);
    }
}
