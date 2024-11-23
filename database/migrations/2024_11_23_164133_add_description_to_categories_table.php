<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the 'description' column to the 'categories' table
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        // Update the descriptions for predefined rows
        DB::table('categories')->where('internal_name', 'proposals')->update([
            'description' => 'Comparte tus propuestas de mejora y sugerencias con la comunidad.',
        ]);

        DB::table('categories')->where('internal_name', 'polls')->update([
            'description' => 'Crea encuestas para conocer la opinión de la comunidad.',
        ]);

        DB::table('categories')->where('internal_name', 'imagine')->update([
            'description' => 'Comparte ideas creativas para mejorar la comunidad.',
        ]);

        // Insert a new category: Solicitudes
        DB::table('categories')->insert([
            'display_name' => 'Solicitudes',
            'internal_name' => 'requests',
            'icon' => '📝',
            'description' => 'Plantea una preocupación o situación que afecta a la comunidad.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // Remove the 'Solicitudes' category
        DB::table('categories')->where('internal_name', 'requests')->delete();

        // Remove the 'description' column
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};