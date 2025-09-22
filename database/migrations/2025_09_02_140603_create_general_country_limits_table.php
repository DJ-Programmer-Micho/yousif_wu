<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('general_country_limits', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_value', 12, 2)->default(0);
            $table->decimal('max_value', 12, 2)->default(0);
            $table->timestamps();
        });

        // Seed default row
        DB::table('general_country_limits')->insert([
            'id' => 1,
            'min_value' => 0,
            'max_value' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('general_country_limits');
    }
};
