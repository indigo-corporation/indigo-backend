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
        Schema::create('compilations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->integer('order');
            $table->timestamps();
        });

        Schema::create('compilation_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('compilation_id')->unsigned();
            $table->string('locale', 3);
            $table->string('title');

            $table->unique(['compilation_id', 'locale']);

            $table->foreign('compilation_id')
                ->references('id')
                ->on('compilations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('compilation_film', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('compilation_id');
            $table->unsignedInteger('film_id');
            $table->integer('order');

            $table->unique(['compilation_id', 'film_id']);

            $table->foreign('compilation_id')
                ->references('id')
                ->on('compilations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compilation_translations');
        Schema::dropIfExists('compilation_film');
        Schema::dropIfExists('compilations');
    }
};
