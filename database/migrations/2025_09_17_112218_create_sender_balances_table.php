<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sender_balances', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete(); // register (role=2)
            $t->decimal('amount', 14, 2);
            $t->enum('status', ['Incoming','Outgoing']); // or ['Incoming','outcoming'] if you prefer
            // nullable FKs
            $t->foreignId('sender_id')->nullable()->constrained('senders')->nullOnDelete();
            $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('note')->nullable();
            $t->timestamps();

            $t->index(['user_id','status']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sender_balances', function (Blueprint $t) {
            // Drop FKs first (names may vary by driver; these are typical)
            try { $t->dropForeign(['sender_id']); } catch (\Throwable $e) {}
            try { $t->dropForeign(['admin_id']); } catch (\Throwable $e) {}

            // Keep columns (still useful), or drop them if you prefer:
            // $t->dropColumn(['sender_id','admin_id']);
        });
    }
};
