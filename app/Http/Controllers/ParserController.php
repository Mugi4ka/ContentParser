<?php

namespace App\Http\Controllers;

use DiDom\Document;
use ErrorException;
use Illuminate\Database\Schema\Blueprint;
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
        $fl_arrays = preg_grep("/(dushevoy-ugolok|shtorka|dver-v-proyem|dushevaya-peregorodka)/i", $testPers->toArray());
        foreach ($fl_arrays as $fl_array) {
            $document = new Document($fl_array, true);
            $posts = $document->find('.product-item-detail-properties-name::text');
            $values = $document->find('.product-item-detail-properties-val::text');
            $valuesVendor = $document->find('.product-item-detail-properties-val > a::text');
            $description = $document->find('.product-item-detail-preview::text');
            $description = implode($description);
            $productName = $document->find('.navigation-title::text');
            $productName = implode($productName);
            if ($posts) {
                try {
                    array_splice($values, 0, 0, $valuesVendor[0]);
                    array_splice($values, 2, 0, $valuesVendor[1]);
                } catch (ErrorException $e) {
                    continue;
                }
                unset($posts[2]);
                unset($values[2]);
//            $posts = array_unique($posts);
//            $values = array_unique($values);
                $posts = array_map(function ($posts) {
                    return trim($posts);
                }, $posts);
                $values = array_map(function ($values) {
                    return trim($values);
                }, $values);
                $posts = $name = str_replace('.', '', $posts);
                $ars = array_combine($posts, $values);
                $ars += ['Название' => $productName];
                $ars += ['Описание' => $description];
                foreach ($ars as $key => $value) {
                    $key =  trim($key);
                    if (!Schema::hasColumn('contents', $key)) {
                        Schema::table('contents', function (Blueprint $table) use ($key) {
                            $table->text($key)->nullable();
                        });
                    }
                }

                DB::table('contents')->insert($ars);
            }
        }
    }
}
