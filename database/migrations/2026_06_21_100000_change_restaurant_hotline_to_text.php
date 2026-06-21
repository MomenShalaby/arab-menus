<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The scraper joins every branch phone number into a single `hotline` string
 * (e.g. "19223 - 0238379706 - 0223826722 - ..."). Large chains overflow the
 * original varchar(255), so the detail-scrape UPDATE was failing with
 * "SQLSTATE[22001] Data too long for column 'hotline'" — the single biggest
 * source of failed scraping_logs. Widen it to TEXT so the full list always fits.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table): void {
            $table->text('hotline')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table): void {
            $table->string('hotline')->nullable()->change();
        });
    }
};
