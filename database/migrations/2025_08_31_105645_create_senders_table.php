<?php

// database/migrations/2025_09_01_000000_create_senders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('senders', function (Blueprint $t) {
      $t->engine = 'InnoDB';
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      // person info
      $t->string('first_name');
      $t->string('last_name');
      $t->string('phone', 32);
      $t->string('address')->nullable();
      $t->char('country', 2); // ISO2 from countrySelect (e.g. IQ, US, LB)

      // send amounts
      $t->decimal('amount', 12, 2);
      $t->decimal('tax', 12, 2)->default(0.00);
      $t->decimal('total', 12, 2);
      $t->enum('status', ['Executed','Pending','Rejected'])
              ->default('Pending')
              ->index();
      // intended receiver snapshot (optional)
      $t->string('r_first_name')->nullable();
      $t->string('r_last_name')->nullable();
      $t->string('r_phone', 32)->nullable();

      $t->json('payouts');

      $t->timestamps();
      $t->softDeletes();

      $t->index(['last_name','first_name'], 'senders_name_idx');
      $t->index('phone');
      $t->index('country');
      $t->index('created_at');
      $t->index('user_id');
    });
  }

  public function down(): void { Schema::dropIfExists('senders'); }
};
