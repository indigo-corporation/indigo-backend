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
        //get genres
        $genres = [];
        $is_animation = false;
        foreach ($data->genres as $genre) {
            $genreModel = Genre::whereTranslationIlike('title', $genre->name)
                ->where('is_anime', false)
                ->first();
            if ($genreModel) {
                $genres[] = $genreModel->id;

                if ($genreModel->animation === 'animation') {
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

        // imdb
        $link = env('OMDB_API') . '?apikey=' . env('OMDB_KEY') . '&i=' . $imdb_id;
        $data = json_decode(file_get_contents($link));
        $film->poster_url = $data->Poster;
        $film->imdb_id = $imdb_id;
        $film->imdb_rating = $data->imdbRating !== 'N/A' ? $data->imdbRating : null;
        $film->year = is_int((int)$data->Year) ? (int)$data->Year : null;
        $film->runtime = $data->Runtime !== 'N/A'
            ? explode(' ', $data->Runtime)[0]
            : null;

        $film->save();

        $film->genres()->attach($genres);
        $film->countries()->attach($countries);
    }
}
