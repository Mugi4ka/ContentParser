<?php


namespace App\Classes\Vendors;


use App\Interfaces\ContentInterface;
use App\Models\Link;
use DiDom\Document;

class Parly implements ContentInterface
{

    private $keyWords;

    public function __construct($keyWords)
    {
        $this->keyWords = $keyWords;
    }

    /**
     * @return mixed
     */
    public function getKeyWords(): string
    {
        return $this->keyWords;
    }

    public function parseContent()
    {
        $resultArray = [];
        $combined = [];
        $queryString = $this->getKeyWords();
        $links = Link::get()->pluck('link');
        $neededLinks = preg_grep("/($queryString)/i", $links->toArray());
        foreach ($neededLinks as $neededLink) {
            try {
                $linkImage = [];
                $document = new Document($neededLink, true);
                $name = $document->first('h1::text');
                $description = $document->first('.product-anonce::text');
                $sku = $document->first('.shop2-product-article::text');
                $vendor = $document->find('.shop-product-options > div')[0];
                $vendor = $vendor->first('a::text');
                $images = $document->find('.thumb-item');
                foreach ($images as $image) {
                    $rawImageLink = $image->first('a')->getAttribute('href');
                    $linkImage[] = 'https://xn-----6kcbafdobhdh0bza2cctab1bzc6grh.xn--p1ai/' . $rawImageLink;
                }
                $images = implode('#', $linkImage);
                $propertiesBlock = $document->find('.shop-product-params > div');
                foreach ($propertiesBlock as $propertyBlock) {
                    $property = $propertyBlock->first('.param-title::text');
                    $value = $propertyBlock->first('.param-body::text');
                    $property = str_replace(' ', '_', $property);
                    $property = str_replace('.', '_', $property);
                    $combined[$property] = $value;
                }
                $combined['Название'] = $name;
                $combined['Описание'] = $description;
                $combined['Производитель'] = $vendor;
                $combined['Артикул'] = $sku;
                $combined['Изображения'] = $images;
                $resultArray[] = $combined;
            } catch (\Exception $e) {
                continue;
            }
        }
        return $resultArray;
    }
}
