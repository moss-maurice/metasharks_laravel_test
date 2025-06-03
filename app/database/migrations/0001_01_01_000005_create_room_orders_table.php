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
        Schema::create('room_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('room_id')->index();
            $table->date('date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['room_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_orders');
    }
};
