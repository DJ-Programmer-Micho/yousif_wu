<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // reusable bracket sets
        Schema::create('tax_bracket_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('brackets_json'); // e.g. [[1,50,8],[51,200,13]]
            $table->timestamps();
        });

        // assign a bracket set to each country
        Schema::create('country_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('tax_bracket_set_id')->constrained('tax_bracket_sets')->cascadeOnUpdate();
            $table->timestamps();

            $table->unique('country_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('country_taxes');
        Schema::dropIfExists('tax_bracket_sets');
    }
};
