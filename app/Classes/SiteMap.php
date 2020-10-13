<?php


namespace App\Classes;


use App\Http\Requests\XmlLinkRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;

class SiteMap implements \App\Interfaces\SiteMapInterface
{

    public function createSiteMap($siteLink)
    {
        //Belbagno
//        $path = storage_path('app/sitemap'.$siteLink.'.xml');
//        SitemapGenerator::create($siteLink)
//            ->hasCrawled(function (Url $url) {
//                if ($url->segment(1) == 'product') {
//                    $url->setPriority(1.0);
//                    return $url;
//                } else {
//                    return "";
//                }
//            })
//            ->writeToFile($path);
//        $this->getLinks();

        $path = storage_path('app/Rostov.xml');
        SitemapGenerator::create($siteLink)
            ->hasCrawled(function (Url $url) {
                if ($url->segment(1) == 'catalog') {
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
//        $itemLinks = [];
//        $xml = XmlParser::load(storage_path('app/sitemap.xml'));
//        $products = $xml->getContent();
//        foreach ($products as $product) {
//            $itemLinks[] = $product->loc->__toString();
//        }
//        foreach ($itemLinks as $itemLink) {
//            try {
//                DB::table('links')->insert(['link' => $itemLink]);
//            }
//            catch (\Exception $exception) {
//                continue;
//            }
//        }
        $itemLinks = [];
        $xml = XmlParser::load(storage_path('app/Rostov.xml'));
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
