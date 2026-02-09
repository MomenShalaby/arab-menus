<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add updated_at_source to restaurants
        Schema::table('restaurants', function (Blueprint $table): void {
            $table->date('updated_at_source')->nullable()->after('last_scraped_at');
        });

        // Create branches table
        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('address')->nullable();
            $table->string('address_ar')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamps();

            $table->index('restaurant_id');
        });

        // Add name_ar to categories if not existing
        if (! Schema::hasColumn('categories', 'name_ar')) {
            // Already exists
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');

        Schema::table('restaurants', function (Blueprint $table): void {
            $table->dropColumn('updated_at_source');
        });
    }
};
