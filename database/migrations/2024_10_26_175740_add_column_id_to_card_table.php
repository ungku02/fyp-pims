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
        Schema::table('card', function (Blueprint $table) {
            $table->unsignedBigInteger('column_id')->nullable()->after('id');
            $table->foreign('column_id')->references('id')->on('column')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card', function (Blueprint $table) {
            //
        });
    }
};
