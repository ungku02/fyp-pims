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
        Schema::create('swap_cards', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('status'); // Status column
            $table->foreignId('old_user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table for old_user_id
            $table->foreignId('new_user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table for new_user_id
            $table->foreignId('card_id')->constrained('card')->onDelete('cascade'); // Foreign key to cards table for card_id
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_cards');
    }
};
