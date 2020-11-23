<?php


namespace App\Classes\Vendors;


use App\Models\Link;
use DiDom\Document;

class DK implements \App\Interfaces\ContentInterface
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
                $images = $document->find('a.img-middle');
                foreach ($images as $image) {
                    $rawImageLink = $image->getAttribute('href');
                    $linkImage[] = 'https://dk-de.ru' . $rawImageLink;
                }
                $images = implode('#', $linkImage);
                $description = $document->find('.pp-section::text')[1];
                $propertiesBlock = $document->find('#pp-specs>.product-specs>dl');
                foreach ($propertiesBlock as $propertyBlock) {
                    $property = $propertyBlock->first('dt>span::text');
                    $property = str_replace('.', '_', $property);
                    $property = str_replace(' ', '_', $property);
                    $value = $propertyBlock->first('dd>span::text');
                    if ($property == 'Коллекция') {
                        $value = $propertyBlock->first('dd>span>a::text');
                    }
                    $combined[$property] = $value;
                }
                $combined['Описание'] = $description;
                $combined['Изображения'] = $images;
                $combined['Название'] = $name;
                $resultArray[] = $combined;
            } catch (\Exception $e) {
                continue;
            }
        }
        return $resultArray;
    }
}
