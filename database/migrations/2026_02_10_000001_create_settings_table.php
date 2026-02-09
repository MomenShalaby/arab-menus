<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'ads_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ads_header_code', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ads_sidebar_code', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ads_footer_code', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ads_between_restaurants_code', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
