<?php


namespace App\Classes\Vendors;


use App\Models\Link;
use DiDom\Document;

class Frap implements \App\Interfaces\ContentInterface
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
        $propertiesArray = [];
        $valuesArray = [];
        $precombined = [];
        $queryString = $this->getKeyWords();
        $links = Link::get()->pluck('link');
        $neededLinks = preg_grep("/($queryString)/i", $links->toArray());
        foreach ($neededLinks as $neededLink) {
            try {
                $linkImage = [];
                $document = new Document($neededLink, true);
                $name = $document->first('h1>span::text');
                $images = $document->find('.thumbnail');
                foreach ($images as $image) {
                    $rawImageLink = $image->getAttribute('href');
                    $linkImage[] = $rawImageLink;
                }
                $images = implode('#', $linkImage);
                $properties = $document->find('.tab-pane > div');
//                foreach ($properties as $propertyBlock) {
//                    $property = $propertyBlock->first('span::text');
//                    $property = str_replace('.', '_', $property);
//                    $property = trim(str_replace(' ', '_', $property));
//                    $value = $propertyBlock->text();
//                    $precombined[$property] = $value;
//                }
                $model = $document->find('[itemprop]')[7]->text();
//                dd($model, $name, $images, $precombined);
                $combined['Название'] = $name;
//                $combined['Гарантия'] = $precombined['ГАРАНТИЙНЫЙ_СРОК:'];
//                $combined['Управление'] = $precombined['УПРАВЛЕНИЕ:'];
//                $combined['Комплектация'] = $precombined['В_КОМПЛЕКТЕ:'];
//                $combined['МАТЕРИАЛ'] = $precombined['МАТЕРИАЛ:'];
                $combined['Изображения'] = $images;
                $combined['Производитель'] = 'Frap';
                $combined['Модель'] = $model;
                $resultArray[] = $combined;

            } catch (\Exception $e) {
                continue;
            }
        }
        return $resultArray;
    }
}
