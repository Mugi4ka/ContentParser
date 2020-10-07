<?php


namespace App\Classes;


use App\Http\Requests\XmlLinkRequest;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SiteMap implements \App\Interfaces\SiteMapInterface
{

    public function createSiteMap($request)
    {
        $path = storage_path('app/sitemap.xml');
        SitemapGenerator::create($request->sitemap)
            ->hasCrawled(function (Url $url) {
                if ($url->segment(1) == 'product') {
                    $url->setPriority(1.0);
                    return $url;
                } else {
                    return "";
                }
            })
            ->setMaximumCrawlCount ( 10 )
            ->writeToFile($path);
    }
}
