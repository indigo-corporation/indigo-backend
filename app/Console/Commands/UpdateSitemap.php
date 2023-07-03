<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateSitemap extends Command
{
    protected $signature = 'update-sitemap';

    protected $description = 'update-sitemap';

    private $sitemapsCount;
    const DOMEN = 'https://indigofilms.online';

    const PATH = '/var/www/indigofilms.online';

    const PER_FILE = 25000;

    public function handle(): void
    {
        $this->siteMap1();

        $filmsCount = Film::count();
        $needFiles = (int)ceil($filmsCount / self::PER_FILE);

        for ($i = 1; $i <= $needFiles; $i++) {
            $this->siteMapFilms($i);
        }

        $this->siteMapMain($needFiles);
    }

    private function siteMapMain(int $filmFilesCount): void
    {
        $fileName = 'sitemap.xml';
        file_put_contents(self::PATH . '/' . $fileName, '');

        $data = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<sitemap>
<loc>' . self::DOMEN . '/sitemap1.xml</loc>
<lastmod>' . Carbon::now()->toDateString() . '</lastmod>
</sitemap>';

        for ($i = 1; $i <= $filmFilesCount; $i++) {
            $data .= '<sitemap>
<loc>' . self::DOMEN . '/sitemap' . $i + 1 . '.xml</loc>
<lastmod>' . Carbon::now()->toDateString() . '</lastmod>
</sitemap>';
        }

        $data .= '</sitemapindex>';
        file_put_contents(self::PATH . '/' . $fileName, $data);
    }

    private function siteMap1(): void
    {
        $fileName = 'sitemap1.xml';
        file_put_contents(self::PATH . '/' . $fileName, '');

        $fp = fopen(self::PATH . '/' . $fileName, 'a+');

        $data = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $data .= $this->getXml(self::DOMEN, 1);

        foreach (Film::CATEGORIES as $category) {
            $url = $this->getCategoryUrl($category);
            $data .= $this->getXml($url, 0.8);
        }

        foreach (Film::CATEGORIES as $category) {
            $genreSlugs = Genre::where('is_anime', $category === Film::CATEGORY_ANIME)
                ->where('is_hidden', false)
                ->pluck('slug')
                ->toArray();

            foreach ($genreSlugs as $slug) {
                $url = $this->getGenreUrl($category, $slug);
                $data .= $this->getXml($url, 0.7);
            }
        }

        $countries = Country::getList();
        foreach (Film::CATEGORIES as $category) {
            if ($category === Film::CATEGORY_ANIME) continue;

            foreach ($countries as $country) {
                $url = $this->getCountryUrl($category, $country->slug);
                $data .= $this->getXml($url, 0.7);
            }
        }

        foreach (Film::CATEGORIES as $category) {
            for ($year = 1910; $year <= date("Y"); $year++) {
                $url = $this->getYearUrl($category, $year);
                $data .= $this->getXml($url, 0.7);
            }
        }

        $data .= '</urlset>';

        fwrite($fp, $data);
        fclose($fp);
    }

    private function siteMapFilms(int $number): void
    {
        $fileName = 'sitemap' . $number + 1 . '.xml';
        file_put_contents(self::PATH . '/' . $fileName, '');

        $fp = fopen(self::PATH . '/' . $fileName, 'a+');

        $data = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        fwrite($fp, $data);

        $filmIds = Film::orderBy('id', 'desc')
            ->where('is_hidden', false)
            ->offset(self::PER_FILE * ($number - 1))
            ->limit(self::PER_FILE)
            ->pluck('id');

        $chunkSize = 250;
        $i = 0;
        Film::with(['translations'])
            ->where('id', '>=', $filmIds->last())
            ->where('id', '<=', $filmIds->first())
            ->where('is_hidden', false)
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$fp, &$i, $chunkSize) {
                $data = '';
                foreach ($films as $film) {
                    $url = $this->getFilmUrl($film->category, $film->slug);
                    $data .= $this->getXml($url, 0.5);
                }

                dump(
                    $chunkSize * ++$i
                );

                fwrite($fp, $data);
            });

        $data = '</urlset>';
        fwrite($fp, $data);

        fclose($fp);
    }

    private function getXml(string $url, float $priority = 0.5): string
    {
        return '<url>
<loc>' . $url . '</loc>
<lastmod>' . Carbon::now()->toDateString() . '</lastmod>
<priority>' . $priority . '</priority>
</url>';
    }

    private function getCategoryUrl(string $category): string
    {
        return self::DOMEN . '/' . $category;
    }

    private function getGenreUrl(string $category, string $slug): string
    {
        return self::DOMEN . '/' . $category . '/genre/' . $slug;
    }

    private function getCountryUrl(string $category, string $slug): string
    {
        return self::DOMEN . '/' . $category . '/country/' . $slug;
    }

    private function getYearUrl(string $category, int $year): string
    {
        return self::DOMEN . '/' . $category . '/year/' . $year;
    }

    private function getFilmUrl(string $category, string $slug): string
    {
        return self::DOMEN . '/' . $category . '/' . $slug;
    }
}
