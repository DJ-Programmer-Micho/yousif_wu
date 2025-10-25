<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();

            // FK to countries table
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            // Use short codes ("CA", "NY", "BC", "AB" …)
            $table->string('code', 3);  // 2–3 chars to cover all cases

            // Localized names
            $table->string('en_name');
            $table->string('ar_name')->nullable();
            $table->string('ku_name')->nullable();

            $table->timestamps();

            // Prevent duplicates inside one country
            $table->unique(['country_id', 'code']);
            $table->index('en_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
