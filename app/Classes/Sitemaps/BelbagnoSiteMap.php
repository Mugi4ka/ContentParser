<?php


namespace App\Classes\Sitemaps;


use Illuminate\Support\Facades\DB;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use App\Interfaces\SiteMapInterface;

class BelbagnoSiteMap implements SiteMapInterface
{

    public function createSiteMap($siteLink)
    {
        $path = storage_path('app/Belbagno.xml');
        SitemapGenerator::create($siteLink)
            ->hasCrawled(function (Url $url) {
                if ($url->segment(1) == 'product') {
                    $url->setPriority(1.0);
                    return $url;
                } else {
                    return "";
                }
            })
            ->writeToFile($path);
        $this->getLinks();
    }

    public function getLinks()
    {
        $itemLinks = [];
        $xml = XmlParser::load(storage_path('app/Belbagno.xml'));
        $products = $xml->getContent();
        foreach ($products as $product) {
            $itemLinks[] = $product->loc->__toString();
        }
        foreach ($itemLinks as $itemLink) {
            try {
                DB::table('links')->insert(['link' => $itemLink]);
            }
            catch (\Exception $exception) {
                continue;
            }
        }
    }
}
