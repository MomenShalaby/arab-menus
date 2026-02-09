<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraping_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('type'); // 'city', 'zone', 'restaurant', 'menu_image'
            $table->string('url');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedInteger('items_scraped')->default(0);
            $table->unsignedInteger('retry_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraping_logs');
    }
};
