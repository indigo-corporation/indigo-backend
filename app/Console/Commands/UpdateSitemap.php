<?php

namespace App\Console\Commands;

use App\Jobs\PrenderInFileJob;
use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;

class UpdateSitemap extends Command
{
    protected $signature = 'update-sitemap';

    protected $description = 'update-sitemap';

    const DOMEN = 'https://indigofilms.online';


    public function handle(): void
    {
        $path = '/var/www/indigofilms.online';
        $fileName = 'sitemap.xml';

        $data = '<?xml version="1.0" encoding="UTF-8"?>
                <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <sitemap>
                <loc>' . self::DOMEN . '/sitemap1.xml</loc>
                <lastmod>' . Carbon::now()->toDateString() . '</lastmod>
                </sitemap>
                <sitemap>
                <loc>' . self::DOMEN . '/sitemap2.xml</loc>
                <lastmod>' . Carbon::now()->toDateString() . '</lastmod>
                </sitemap>
                <sitemap>
                <loc>' . self::DOMEN . '/sitemap3.xml</loc>
                <lastmod>' . Carbon::now()->toDateString() . '</lastmod>
                </sitemap>
                </sitemapindex>';

        file_put_contents($path . '/' . $fileName, $data);

        $this->siteMap1();
    }


    private function siteMap1(): void
    {
        $path = '/var/www/indigofilms.online';
        $fileName = 'sitemap1.xml';

        $fp = fopen($path . '/' . $fileName, 'a+');

        $data = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach (Film::CATEGORIES as $category) {
            $url = self::DOMEN . '/' . $category;
            $data .= $this->getXml($url, 0.7);

            $genres = Genre::where('is_anime', $category === Film::CATEGORY_ANIME)
                ->pluck('slug')
                ->toArray();

            foreach ($genres as $genre) {
                $url = self::DOMEN . '/' . $category . '/genre/' . $genre;
                $data .= $this->getXml($url, 0.7);
            }
        }

        $data .= '</urlset>';

        fwrite($fp, $data);
        fclose($fp);
    }

    private function getXml(string $url, float $priority): string
    {
        return '<url><loc>' . $url . '</loc><lastmod>' . Carbon::now()->toDateString() . '</lastmod><priority>' . $priority . '</priority></url>';
    }
}
