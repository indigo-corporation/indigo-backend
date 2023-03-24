<?php

namespace App\Services;

use Illuminate\Support\Str;

class GetFromUrlService
{
    public function getTmdbFilmItems($page, $dump = false)
    {
        $url = env('TMDB_API') . 'movie/top_rated'
            . '?api_key=' . env('TMDB_KEY')
            . '&page=' . $page;

        $data = $this->get($url, $dump);

        return $data->results;
    }

    public function getTmdbFilmItem($id, $dump = false)
    {
        $url = env('TMDB_API') . 'movie/' . $id
            . '?api_key=' . env('TMDB_KEY');

        return $this->get($url, $dump);
    }

    public function getCdnFilmItems($page, $dump = false)
    {
        $url = env('VIDEOCDN_API') . 'movies'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&ordering=created&direction=desc'
            . '&limit=100&page=' . $page;

        $data = $this->get($url, $dump);

        return $data->data;
    }

    public function getCdnSerialItems($page, $dump = false)
    {
        $url = env('VIDEOCDN_API') . 'tv-series'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&ordering=created&direction=desc'
            . '&limit=100&page=' . $page;

        $data = $this->get($url, $dump);

        return $data->data;
    }

    public function getShikiItems($page, $dump = false)
    {
        $url = 'https://shikimori.one/api/animes' . '?limit=50&page=' . $page . '&order=popularity';

        return $this->get($url, $dump);
    }

    public function getShiki($shikiId, $dump = false)
    {
        $url = 'https://shikimori.one/api/animes/' . $shikiId;

        $shikiResponse = $this->get($url, $dump);

        return isset($shikiResponse->id) ? $shikiResponse : null;
    }

    public function getKodik($shikiId, $dump = false)
    {
        $url = env('KODIK_API') . 'search'
            . '?token=' . env('KODIK_TOKEN')
            . '&shikimori_id=' . $shikiId;

        $kodikResponse = $this->get($url, $dump);

        return $kodikResponse->results[0] ?? null;
    }

    public function getVideocdnFilm($imdbId, $dump = false)
    {
        $url = env('VIDEOCDN_API') . 'movies'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&field=imdb_id&query=' . $imdbId;

        $videocdnResponse = $this->get($url, $dump);

        return $videocdnResponse->data[0] ?? null;
    }

    public function getVideocdnSerial($imdbId, $dump = false)
    {
        $url = env('VIDEOCDN_API') . 'tv-series'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&field=imdb_id&query=' . $imdbId;

        $videocdnResponse = $this->get($url, $dump);

        return $videocdnResponse->data[0] ?? null;
    }

    public function getImdb($imdbId, $dump = false)
    {
        try {
            $imdbData = new \Imdb\Title($imdbId);
        } catch (\Throwable $e) {
            if (Str::contains($e->getMessage(), 'Status code [404]')) {
                if ($dump) {
                    dump('not_found imdb' . $imdbId);
                }

                $imdbData = null;
            } else {
                if ($dump) {
                    dump($e->getMessage());
                    dump('retry imdb ' . $imdbId);
                }

                sleep(5);

                $imdbData = new \Imdb\Title($imdbId);
            }
        }

        return $imdbData;
    }

    public function get($url, $dump = false)
    {
        try {
            $data = $this->curl($url);
        } catch (\Throwable $e) {
            if ($dump) {
                dump($e->getMessage());
                dump('retry ' . $url);
            }

            sleep(5);

            $data = $this->curl($url);
        }

        return json_decode($data);
    }

    private function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result) {
            throw new \Error('response error');
        }

        return $result;
    }
}
