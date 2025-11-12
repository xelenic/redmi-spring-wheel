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
        Schema::create('spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_prize_id')->nullable()->constrained('prizes')->cascadeOnDelete();
            $table->foreignId('awarded_prize_id')->nullable()->constrained('prizes')->cascadeOnDelete();
            $table->boolean('issued')->default(false);
            $table->string('result_label');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spins');
    }
};

