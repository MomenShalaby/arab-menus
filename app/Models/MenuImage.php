<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'original_url',
        'local_path',
        'alt_text',
        'sort_order',
        'width',
        'height',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'file_size' => 'integer',
        ];
    }

    /**
     * Get the restaurant that owns this menu image.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the display URL (local or remote fallback).
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->local_path && file_exists(storage_path('app/public/' . $this->local_path))) {
            return asset('storage/' . $this->local_path);
        }

        return $this->original_url;
    }
}
