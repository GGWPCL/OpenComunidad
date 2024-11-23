<?php

use App\Models\Category;
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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('internal_name')->unique()->change();
            $table->string('icon')->nullable();
        });

        DB::table('categories')->insert([
            [
                'display_name' => 'Propuestas',
                'internal_name' => Category::INTERNAL_NAME_PROPOSALS,
                'icon' => '💡',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'display_name' => 'Encuestas',
                'internal_name' => Category::INTERNAL_NAME_POLLS,
                'icon' => '📊',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'display_name' => 'Imagina',
                'internal_name' => Category::INTERNAL_NAME_IMAGINE,
                'icon' => '🎨',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->whereIn('internal_name', [
            Category::INTERNAL_NAME_PROPOSALS,
            Category::INTERNAL_NAME_POLLS,
            Category::INTERNAL_NAME_IMAGINE,
        ])->delete();

        Schema::table('categories', function (Blueprint $table) {
            $table->string('internal_name')->change();
        });
    }
};
