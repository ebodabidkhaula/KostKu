<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Kost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'address', 'city', 'province',
        'postal_code', 'latitude', 'longitude', 'type', 'price_monthly',
        'price_yearly', 'total_rooms', 'available_rooms', 'facilities',
        'shared_facilities', 'owner_name', 'owner_phone', 'thumbnail',
        'video_tour', 'status', 'is_featured', 'min_stay', 'rules',
        'entry_time', 'exit_time', 'allow_cooking', 'allow_pets',
        'allow_guest', 'created_by'
    ];

    protected $casts = [
        'facilities' => 'array',
        'shared_facilities' => 'array',
        'rules' => 'array',
        'is_featured' => 'boolean',
        'allow_cooking' => 'boolean',
        'allow_pets' => 'boolean',
        'allow_guest' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($kost) {
            if (empty($kost->slug)) {
                $kost->slug = Str::slug($kost->name) . '-' . Str::random(6);
            }
        });
    }

    // --- RELATIONS ---
    public function photos()
    {
        return $this->hasMany(KostPhoto::class)->orderBy('order');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(KostPhoto::class)->where('is_primary', true);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // --- ACCESSORS ---
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            $projectId = 'itkwzhuxntqnisniesrd';
            return "https://{$projectId}.storage.supabase.co/storage/v1/object/public/kostQu/{$this->thumbnail}";
        }
        return asset('images/default-kost.png');
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->avg('rating') ?? 0.0);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price_monthly, 0, ',', '.');
    }

    // --- SCOPES ---
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * FIX: gunakan whereRaw dengan literal SQL "true",
     * bukan ->where('is_featured', true) yang dibind sebagai parameter.
     * Ini menghindari bug pdo_pgsql yang membind boolean PHP sebagai
     * integer tanpa tipe eksplisit, sehingga PostgreSQL melempar:
     * "operator does not exist: boolean = integer"
     */
    public function scopeFeatured($query)
    {
        return $query->whereRaw('is_featured = true');
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('address', 'like', "%{$keyword}%")
              ->orWhere('city', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }
}
