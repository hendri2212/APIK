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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
            $table->decimal('latitude', 10, 7)->default(-3.2252057)->after('uuid');
            $table->decimal('longitude', 10, 7)->default(116.2475487)->after('latitude');
            $table->integer('radius')->default(30)->after('longitude');
            $table->date('expired')->nullable()->after('radius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('radius');
            $table->dropColumn('expired');
        });
    }
};
