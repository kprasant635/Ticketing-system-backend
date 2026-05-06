<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_slas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id');

            $table->timestamp('start_time');

            $table->timestamp('due_time');

            $table->timestamp('completed_time')->nullable();

            $table->boolean('is_breached')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_slas');
    }
};
