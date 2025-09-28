<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // who created it (admin)
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // the message content (long text)
            $table->longText('body');

            // simple show/hide switch
            $table->boolean('is_visible')->default(false)->index();

            // optional window to auto-limit visibility (nullable means "no limit")
            $table->timestamp('show_from')->nullable()->index();
            $table->timestamp('show_until')->nullable()->index();

            // optional targeting (keep simple now; default = everyone)
            // e.g. ["Register"] later if you want per-role targeting
            $table->json('audience_roles')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
