<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('general_country_taxes', function (Blueprint $table) {
            $table->id();
            $table->json('brackets_json')->nullable();
            $table->timestamps();
        });

        // Seed default row
        DB::table('general_country_taxes')->insert([
            'id' => 1,
            'brackets_json' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('general_country_taxes');
    }
};
