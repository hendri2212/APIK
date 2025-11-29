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
        Schema::create('jam_absens', function (Blueprint $table) {
            $table->id();
            $table->time('checkin_time');  // contoh: 07:20:00
            $table->time('checkout_time'); // contoh: 16:30:00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_absens');
    }
};
