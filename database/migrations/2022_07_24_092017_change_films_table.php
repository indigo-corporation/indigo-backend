<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('films', function (Blueprint $table) {
            $table->string('shiki_id', 10)->nullable();
            $table->decimal('shiki_rating', 4, 2)->nullable();
            $table->boolean('is_anime')->default(false);
            $table->boolean('is_serial')->default(false);

            $table->unique('shiki_id');
            $table->unique('imdb_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('shiki_id');
            $table->dropColumn('shiki_rating');
            $table->dropColumn('is_anime');
            $table->dropColumn('is_serial');
        });
    }
};
