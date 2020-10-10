<?php


namespace App\Classes;


use App\Models\Link;
use DiDom\Document;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Interfaces\ContentInterface;

class BelBagno implements ContentInterface
{

    public function parseContent($keyWords)
    {
        $links = Link::get()->pluck('link');
        $neededLinks = preg_grep("/($keyWords)/i", $links->toArray());
        foreach ($neededLinks as $neededLink) {
            $linkImage = [];
            $document = new Document($neededLink, true);
            $vendor = $document->first('.product-item-detail-properties > div > a::text');
            $properties = array_diff($document->find('.product-item-detail-properties-name::text'),
                ["Бренд"]);
            $properties = $name = str_replace('.', '', $properties);
            $properties = array_map(function ($properties) {
                return trim($properties);
            }, $properties);
            $values = $document->find('.product-item-detail-properties-val::text');
            $images = $document->find('.product-item-detail-slider-controls-image > img');
            foreach ($images as $image) {
                $rawImageLink = $image->getAttribute('src');
                $linkImage[] = 'https://belbagno.ru' . $rawImageLink;
            }
            if ($document->first('.product-item-detail-price-old::text')) {
                $price = $document->first('.product-item-detail-price-old::text');
            } else {
                $price = $document->first('.product-item-detail-price-current::text');
            }
            $images = implode('#', $linkImage);
            $description = implode($document->find('.product-item-detail-preview::text'));
            $productName = implode($document->find('.navigation-title::text'));
            $collection = collect($properties);
            $combined = $collection->combine($values);
            $combined['Изображения'] = $images;
            $combined['Описание'] = $description;
            $combined['Название'] = $productName;
            $combined['Производитель'] = $vendor;
            $combined['Цена'] = $price;
            $combined = $combined->toArray();
            if (DB::table('vendors')->where('name', $vendor)->doesntExist() && !is_null($vendor)) {
                DB::table('vendors')->insert(['name' => $vendor]);
            }
            foreach ($combined as $key => $value) {
                if (!Schema::hasColumn('contents', $key)) {
                    Schema::table('contents', function (Blueprint $table) use ($key) {
                        if ($key == 'Артикул') {
                            $table->string('Артикул')->unique();
                        } else {
                            $table->text($key)->nullable();
                        }
                    });
                }
            }
            try {
                DB::table('contents')->insert($combined);
            } catch (QueryException $e) {
                continue;
            }
        }
    }
}
