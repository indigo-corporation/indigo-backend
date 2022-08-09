<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('original_title')->nullable();
            $table->string('original_language', 4)->nullable();
            $table->string('poster_url')->nullable();
            $table->smallInteger('runtime')->nullable();
            $table->date('release_date')->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('imdb_id', 10)->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->timestamps();
        });

        Schema::create('film_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('film_id')->unsigned();
            $table->string('locale', 3);
            $table->string('title');
            $table->text('overview')->nullable();

            $table->unique(['film_id','locale']);

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('cascade');
        });

        Schema::create('film_genre', function (Blueprint $table) {
            $table->id();

            $table->integer('film_id')->unsigned();
            $table->integer('genre_id')->unsigned();

            $table->foreign('genre_id')
                ->references('id')
                ->on('genres')
                ->onDelete('cascade');

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_film');
        Schema::dropIfExists('film_genre');
        Schema::dropIfExists('film_translations');
        Schema::dropIfExists('films');
    }
};
