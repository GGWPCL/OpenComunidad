<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->foreignId('logo_id')->nullable()->constrained('files')->nullOnDelete();
            $table->foreignId('banner_id')->nullable()->constrained('files')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('logo_id');
            $table->dropConstrainedForeignId('banner_id');
        });
    }
};
