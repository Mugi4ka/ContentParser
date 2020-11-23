<?php


namespace App\Classes\Sitemaps;


use Illuminate\Support\Facades\DB;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class ParlySiteMap implements \App\Interfaces\SiteMapInterface
{

    public function createSiteMap($siteLink)
    {
        $path = storage_path('app/parly.xml');
        SitemapGenerator::create($siteLink)
//            ->hasCrawled(function (Url $url) {
//                if ($url->segment(2) == 'dushevye-kabiny-parly') {
//                    $url->setPriority(1.0);
//                    return $url;
//                } else {
//                    return "";
//                }
//            })
            ->writeToFile($path);
        $this->getLinks();
    }

    public function getLinks()
    {
        $itemLinks = [];
        $xml = XmlParser::load(storage_path('app/parly.xml'));
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
