<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table): void {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->string('source_url', 2048)->nullable();
            $table->string('source_author', 120)->nullable();
            $table->date('source_published_at')->nullable();
            $table->string('travel_period', 120)->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
            $table->string('budget_note', 255)->nullable();
            $table->string('photo_credit', 255)->nullable();
        });
    }

    /** Imported reports may omit exact travel dates, so date columns remain nullable on rollback. */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table): void {
            $table->dropColumn(['source_url', 'source_author', 'source_published_at', 'travel_period', 'duration_days', 'budget_note', 'photo_credit']);
        });
    }
};
