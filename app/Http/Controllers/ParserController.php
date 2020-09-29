<?php

namespace App\Http\Controllers;

use DiDom\Document;
use DiDom\Query;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Orchestra\Parser\Xml\Facade as XmlParser;

class ParserController extends Controller
{
    public $user = [];

    public function test()
    {
        $response = Http::get('https://belbagno.ru/');
        dd($response->status());
    }

    public function sitemapGenerator()
    {
        $path = storage_path('app/sitemap.xml');
//        SitemapGenerator:: create('https://belbagno.ru/')
//            ->configureCrawler(function (Crawler $crawler) {
//                $crawler->setMaximumDepth(3);
//            })
//            ->writeToFile($path);

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

    public function xmlAdapt()
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

    public function parseContent()
    {
        $testPers = DB::table('links')->pluck('link');

        foreach ($testPers as $testPer) {

            $document = new Document('https://belbagno.ru/product/akrilovaya-vanna-belbagno-bb101-120-70/', true);
            $posts = $document->find('.product-item-detail-properties-name::text');
            $values = $document->find('.product-item-detail-properties-val::text');
            $valuesVendor = $document->find('.product-item-detail-properties-val > a::text');
            array_splice($values, 0, 0, $valuesVendor[0]);
            array_splice($values, 2, 0, $valuesVendor[1]);
            $posts = array_unique($posts);
            $values = array_unique($values);
            $posts = array_map(function($posts) {
                return trim($posts);
            }, $posts);
            $values = array_map(function($values) {
                return trim($values);
            }, $values);
            $ars = array_combine($posts, $values);
            foreach ($ars as $key => $value) {
                $key= trim($key);
                if (!Schema::hasColumn('contents' ,$key)) {
                    Schema::table('contents', function (Blueprint $table) use ($key) {
                        $table->text($key)->nullable();
                    });
                }
            }
            DB::table('contents')->insert($ars);

        }
    }
}
