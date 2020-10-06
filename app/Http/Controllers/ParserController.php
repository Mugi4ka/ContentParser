<?php

namespace App\Http\Controllers;

use App\Models\Link;
use DiDom\Document;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Orchestra\Parser\Xml\Facade as XmlParser;

class ParserController extends Controller
{
    public function getSiteMap()
    {
        $path = storage_path('app/sitemap.xml');
        SitemapGenerator::create('https://belbagno.ru')
            ->hasCrawled(function (Url $url) {
                if ($url->segment(1) == 'product') {
                    $url->setPriority(1.0);
                    return $url;
                } else {
                    return "";
                }
            })
            ->writeToFile($path);
    }

    public function getLinks()
    {
        $itemLinks = [];
        $xml = XmlParser::load(storage_path('app/sitemap.xml'));
        $products = $xml->getContent();

        foreach ($products as $product) {
            $itemLinks[] = $product->loc->__toString();
        }
        foreach ($itemLinks as $itemLink) {
            DB::table('links')->insert(['link' => $itemLink]);
        }
    }

    public function getContent()
    {
        $links = Link::get()->pluck('link');
        $neededLinks = preg_grep("/(unitaz-pristavnoy|unitaz-bezobodkovyy-pristavnoy|unitaz-podvesnoy|unitaz-bezobodkovyy-podvesnoy|unitaz-kompakt-bezobodkovyy|unitaz-bezobodkovyy-kompakt|unitaz-kompakt)/i", $links->toArray());
        foreach ($neededLinks as $neededLink) {
            $document = new Document($neededLink, true);
            $vendor = $document->first('.product-item-detail-properties > div > a::text');
            $properties = array_diff($document->find('.product-item-detail-properties-name::text'),
                ["Бренд"]);
            $properties = $name = str_replace('.', '', $properties);
            $properties = array_map(function ($properties) {
                    return trim($properties);
                }, $properties);
            $values = $document->find('.product-item-detail-properties-val::text');
            $description = implode($document->find('.product-item-detail-preview::text'));
            $productName = implode($document->find('.navigation-title::text'));
            $collection = collect($properties);
            $combined = $collection->combine($values);
            $combined['Описание'] = $description;
            $combined['Название'] = $productName;
            $combined['Производитель'] = $vendor;
            $combined = $combined->toArray();
            foreach ($combined as $key => $value) {
                    if (!Schema::hasColumn('contents', $key)) {
                        Schema::table('contents', function (Blueprint $table) use ($key) {
                            $table->text($key)->nullable();
                       });
                    }
                }
                DB::table('contents')->insert($combined);
        }
    }
}
