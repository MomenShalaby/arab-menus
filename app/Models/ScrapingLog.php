<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'url',
        'status',
        'error_message',
        'items_scraped',
        'retry_count',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'items_scraped' => 'integer',
            'retry_count' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Mark this log entry as running.
     */
    public function markRunning(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark this log entry as completed.
     */
    public function markCompleted(int $itemsScraped = 0): void
    {
        $this->update([
            'status' => 'completed',
            'items_scraped' => $itemsScraped,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark this log entry as failed.
     */
    public function markFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'retry_count' => $this->retry_count + 1,
            'completed_at' => now(),
        ]);
    }
}
