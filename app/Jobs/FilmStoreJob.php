<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FilmStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $film;

    public function __construct($film)
    {
        $this->film = $film;
    }

    public function handle()
    {
        $item = $this->film;

        $film = new Film([
            'original_title' => $item->original_title,
            'original_language' => $item->original_language,
            'release_date' => $item->release_date,
            'ru' => [
                'title' => $item->title,
                'overview' => $item->overview,
            ]
        ]);

        // film details
        $link = env('TMDB_API') . 'movie/' . $item->id
            . '?api_key=' . env('TMDB_KEY') . '&language=ru';
        $data = json_decode(file_get_contents($link));
        // get imdb id
        $imdb_id = $data->imdb_id;

        $imdbIdExists = Film::where('imdb_id', $imdb_id)->exists();
        if ($imdbIdExists) return;

        //get genres
        $genres = [];
        $is_animation = false;
        foreach ($data->genres as $genre) {
            $genreModel = Genre::whereTranslationIlike('title', $genre->name)
                ->where('is_anime', false)
                ->first();
            if ($genreModel) {
                $genres[] = $genreModel->id;

                if ($genreModel->name === 'animation') {
                    $is_animation = true;
                }
            }
        }
        //get countries
        $countries = [];
        foreach ($data->production_countries as $country) {
            $countryModel = DB::table('countries')->where('iso2', $country->iso_3166_1)->first();
            if ($countryModel) {
                $countries[] = $countryModel->id;

                if ($is_animation && $countryModel->iso2 === 'JP') {
                    $film->is_anime = true;
                }
            }
        }

        try {
            $imdbData = new \Imdb\Title($imdb_id);
            $rating = $imdbData->rating() !== '' ? $imdbData->rating() : null;
            $posterUrl = $imdbData->photo(false) ?? null;
            $runtime = $imdbData->runtime() ?? null;
            $year = $imdbData->year() ?? null;

        } catch (\Throwable $e) {
            if (Str::contains($e->getMessage(), 'Status code [404]')) {
                return;
            } else {
                throw $e;
            }
        }
        dump($imdb_id);
        sleep(15);

        $film->poster_url = $posterUrl;
        $film->imdb_id = $imdb_id;
        $film->imdb_rating = $rating;
        $film->runtime = $runtime;
        $film->year = $year;

        $film->save();

        $film->genres()->attach($genres);
        $film->countries()->attach($countries);
    }
}
