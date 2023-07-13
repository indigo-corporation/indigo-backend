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
        Schema::table('banned_users', function (Blueprint $table) {
            $table->unique(['user_id', 'banned_user_id']);
        });

        Schema::table('favorite_films', function (Blueprint $table) {
            $table->unique(['user_id', 'film_id']);
        });

        Schema::table('film_genre', function (Blueprint $table) {
            $table->unique(['film_id', 'genre_id']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->unique(['user_id', 'comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banned_users', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'banned_user_id']);
        });

        Schema::table('favorite_films', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'film_id']);
        });

        Schema::table('film_genre', function (Blueprint $table) {
            $table->dropUnique(['film_id', 'genre_id']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'comment_id']);
        });
    }
};
