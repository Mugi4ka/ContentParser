<?php


namespace App\Classes\Sitemaps;


use Illuminate\Support\Facades\DB;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class FrapSiteMap implements \App\Interfaces\SiteMapInterface
{

    public function createSiteMap($siteLink)
    {
        $path = storage_path('app/bellis.xml');
        SitemapGenerator::create($siteLink)
            ->writeToFile($path);
        $this->getLinks();
    }

    public function getLinks()
    {
        $itemLinks = [];
        $xml = XmlParser::load(storage_path('app/bellis.xml'));
        $products = $xml->getContent();
        foreach ($products as $product) {
            $itemLinks[] = $product->loc->__toString();
        }
        foreach ($itemLinks as $itemLink) {
            try {
                DB::table('links')->insert(['link' => $itemLink]);
            } catch (\Exception $exception) {
                continue;
            }
        }
    }
}
