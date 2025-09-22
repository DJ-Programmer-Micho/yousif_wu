<?php

// database/migrations/2025_09_17_000002_create_receiver_balances_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('receiver_balances', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id();

            $t->foreignId('user_id')->constrained()->cascadeOnDelete(); // register (role=2)
            $t->decimal('amount', 14, 0); // IQD integer-like
            $t->enum('status', ['Incoming','Outgoing']);

            // nullable FKs
            $t->foreignId('receiver_id')->nullable()->constrained('receivers')->nullOnDelete();
            $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();

            $t->string('note')->nullable();
            $t->timestamps();

            $t->index(['user_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('receiver_balances');
    }
};
