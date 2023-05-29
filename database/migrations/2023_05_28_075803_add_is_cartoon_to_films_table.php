<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Film\Film;
use App\Models\Genre\Genre;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->boolean('is_cartoon')->default(false);
        });

        Film::where('category', Film::CATEGORY_CARTOON)
            ->update(['is_cartoon' => true]);

        Genre::where('name', 'animation')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('is_cartoon');
        });
    }
};
