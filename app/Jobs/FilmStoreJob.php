<?php

namespace App\Jobs;

use App\Models\Country\Country;
use App\Models\Film\Film;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FilmStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $link;

    public function __construct($link = 'movie/top_rated')
    {
        $this->link = $link;
    }

    public function handle()
    {
        $data = json_decode(file_get_contents($this->link));

        foreach ($data->results as $item) {
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
            //get countries
            $countries = [];
            foreach ($data->production_countries as $country) {
                $countryModel = Country::where('code', $country->iso_3166_1)->first();
                if ($countryModel) {
                    $countries[] = $countryModel->id;
                }
            }

            // imdb
            $link = env('OMDB_API') . '?apikey=' . env('OMDB_KEY') . '&i=' . $imdb_id;
            $data = json_decode(file_get_contents($link));
            $film->poster_url = $data->Poster;
            $film->imdb_id = $imdb_id;
            $film->imdb_rating = $data->imdbRating !== 'N/A' ? $data->imdbRating : null;
            $film->year = $data->Year;
            $film->runtime = $data->Runtime !== 'N/A'
                ? explode(' ', $data->Runtime)[0]
                : null;

            $film->save();

            $film->genres()->attach($item->genre_ids);
            $film->countries()->attach($countries);
        }
    }
}