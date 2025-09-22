<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('receivers', function (Blueprint $t) {
        $t->engine = 'InnoDB';
        $t->id();
        $t->char('mtcn',10)->unique();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();

        // Person info
        $t->string('first_name');
        $t->string('last_name');
        $t->string('phone', 32);
        $t->string('address')->nullable();

        // Money (IQD)
        $t->decimal('amount_iqd', 12, 0)->comment('Amount in IQD');

        // Identification payload (nullable for now; can later store type/number/images)
        $t->json('identification')->nullable();
        $t->enum('status', ['Executed','Pending','Rejected'])->default('Pending')->index();
        $t->timestamps();
        $t->softDeletes();

        // Helpful indexes
        $t->index(['last_name', 'first_name'], 'receivers_name_idx');
        $t->index('phone');
        $t->index(['user_id', 'created_at']);
    });
  }
  public function down(): void { Schema::dropIfExists('receivers'); }
};
