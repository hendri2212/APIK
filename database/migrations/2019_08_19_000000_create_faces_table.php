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
        Schema::create('faces', function (Blueprint $table) {
            $table->id();
            // $table->string('uuid')->unique();
            // $table->text('connection');
            $table->string('face_name');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->longText('payload');
            // $table->longText('exception');
            // $table->timestamp('failed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('faces');
    }
};
