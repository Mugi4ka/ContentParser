<?php


namespace App\Classes\Vendors;


use App\Models\Link;
use DiDom\Document;

class Terminus implements \App\Interfaces\ContentInterface
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

    public function parseContent(): array
    {
        $resultArray = [];
        $combined = [];
        $propertiesArray = [];
        $valuesArray = [];
        $queryString = $this->getKeyWords();
        $links = Link::get()->pluck('link');
        $neededLinks = preg_grep("/($queryString)/i", $links->toArray());
        foreach ($neededLinks as $neededLink) {
            try {
                $linkImage = [];
                $document = new Document($neededLink, true);
                $name = $document->first('h1::text');
                $images = $document->find('.product-detail-slider-image > img');
                foreach ($images as $image) {
                    $rawImageLink = $image->getAttribute('src');
                    $linkImage[] = 'https://www.terminus.ru' . $rawImageLink;
                }
                $images = implode('#', $linkImage);
                $properties = $document->find('dt');
                $values = $document->find('dd');
                foreach ($properties as $property) {
                    $property = $property->text();
                    $property = str_replace('.', '_', $property);
                    $property = trim(str_replace(' ', '_', $property));
                    $propertiesArray[] = $property;
                }
                foreach ($values as $value) {
                    $value = $value->text();
                    $valuesArray[] = trim($value);
                }
                $combined = array_combine($propertiesArray, $valuesArray);
                $combined['Изображения'] = $images;
                $combined['Название'] = $name;
                $combined['Производитель'] = 'Terminus';
                $resultArray[] = $combined;
            } catch (\Exception $e) {
                continue;
            }
        }
        return $resultArray;
    }
}
