<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('local_logo_path')->nullable();
            $table->string('hotline')->nullable();
            $table->string('source_url')->nullable();
            $table->unsignedBigInteger('total_views')->default(0);
            $table->timestamp('last_scraped_at')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        // Pivot: restaurant belongs to many cities (branches)
        Schema::create('city_restaurant', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('branch_name')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();

            $table->index(['city_id', 'restaurant_id']);
        });

        // Pivot: restaurant belongs to many zones
        Schema::create('restaurant_zone', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['restaurant_id', 'zone_id']);
        });

        // Pivot: restaurant belongs to many categories
        Schema::create('category_restaurant', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'restaurant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_restaurant');
        Schema::dropIfExists('restaurant_zone');
        Schema::dropIfExists('city_restaurant');
        Schema::dropIfExists('restaurants');
    }
};
