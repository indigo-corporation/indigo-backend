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
            $table->dropColumn('poster_url');

            $table->string('poster_small')->nullable();
            $table->string('poster_medium')->nullable();
            $table->string('poster_large')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('poster_small');
            $table->dropColumn('poster_medium');
            $table->dropColumn('poster_large');

            $table->string('poster_url')->nullable();
        });
    }
};
