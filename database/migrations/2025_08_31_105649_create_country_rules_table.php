<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('country_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnUpdate();
            $table->boolean('rule'); // adjust rule fields as needed
            $table->timestamps();

            $table->unique('country_id'); // one rule per country
        });
    }

    public function down(): void {
        Schema::dropIfExists('country_rules');
    }
};
