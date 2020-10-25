<?php


namespace App\Classes\Vendors;


use App\Models\Link;
use DiDom\Document;

class Niagara implements \App\Interfaces\ContentInterface
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
                $name = $document->first('.h1-prod-name::text');
                $images = $document->find('.thumbnail');
                foreach ($images as $image) {
                    $rawImageLink = $image->getAttribute('href');
                    $linkImage[] = $rawImageLink;
                }
                $images = implode('#', $linkImage);
                $propertiesBlock = $document->find('#tab-specification > table > tbody > tr');
                foreach ($propertiesBlock as $propertyBlock) {
                    $property = $propertyBlock->find('td::text')[0];
                    $property = str_replace(' ', '_', $property);
                    $property = str_replace('.', '_', $property);
                    $value = $propertyBlock->find('td::text')[1];
                    $combined[$property] = $value;
                }
                $combined['Изображения'] = $images;
                $combined['Название'] = $name;
                $combined['Производитель'] = 'Niagara';
                $resultArray[] = $combined;
            } catch (\Exception $e) {
                continue;
            }
        }
        return $resultArray;
    }
}
