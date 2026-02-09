<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'logo_url',
        'local_logo_path',
        'hotline',
        'source_url',
        'total_views',
        'last_scraped_at',
        'updated_at_source',
    ];

    protected function casts(): array
    {
        return [
            'last_scraped_at' => 'datetime',
            'updated_at_source' => 'date',
            'total_views' => 'integer',
        ];
    }

    /**
     * Get all menu images for this restaurant.
     */
    public function menuImages(): HasMany
    {
        return $this->hasMany(MenuImage::class)->orderBy('sort_order');
    }

    /**
     * Get all branches for this restaurant.
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get all cities where this restaurant has branches.
     */
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'city_restaurant')
            ->withPivot(['branch_name', 'address'])
            ->withTimestamps();
    }

    /**
     * Get all zones this restaurant is in.
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'restaurant_zone')
            ->withTimestamps();
    }

    /**
     * Get all categories this restaurant belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_restaurant')
            ->withTimestamps();
    }

    /**
     * Get the logo URL (local or remote fallback).
     */
    public function getLogoAttribute(): string
    {
        if ($this->local_logo_path && file_exists(storage_path('app/public/' . $this->local_logo_path))) {
            return asset('storage/' . $this->local_logo_path);
        }

        return $this->logo_url ?? asset('images/placeholder-logo.png');
    }

    /**
     * Route model binding by slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
