<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            // ISO 3166-1 alpha-2 (lowercase like 'iq', 'us')
            $table->string('iso_code', 2)->unique();

            // Names (store UTF-8; phpMyAdmin import will handle)
            $table->string('en_name');               // e.g., "Iraq (‫العراق‬‎)" or "Austria"
            $table->string('ar_name')->nullable();   // optional Arabic
            $table->string('ku_name')->nullable();   // optional Kurdish (Sorani)

            // Paths to the cropped flags you generated
            $table->string('flag_path');             // e.g., "flags/iq.png"
            $table->string('flagx2_path');           // e.g., "flagsx2/iq.png"

            $table->timestamps();

            // Helpful index for quick lookups by English name
            $table->index('en_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
