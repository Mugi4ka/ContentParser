<?php


namespace App\Classes\Vendors;


use App\Models\Link;
use DiDom\Document;

class Vitra implements \App\Interfaces\ContentInterface
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
                $images = $document->find('.imagesSliderItem');
                foreach ($images as $image) {
                    $rawImageLink = $image->getAttribute('href');
                    $linkImage[] = 'https://www.santechsystemy.ru' . $rawImageLink;
                }
                $images = implode('#', $linkImage);
//                $description = $document->find('.wf_preview_text::text');
                $propertiesBlock = $document->find('.elementzebra > tr');
                foreach ($propertiesBlock as $propertyBlock) {
                    dd($propertyBlock->find('td>span'));
                    $property = $propertyBlock->find('td>span::text')[0];
                    $property = str_replace('.', '_', $property);
                    $property = str_replace(' ', '_', $property);
                    $value = $propertyBlock->find('td>span::text')[1];
                    $combined[$property] = $value;
                }
//                $combined['Описание'] = $description;
                $combined['Изображения'] = $images;
                $combined['Название'] = $name;
                $resultArray[] = $combined;
                dd($resultArray);
            } catch (\Exception $e) {
                continue;
            }
        }
        dd($resultArray);
        return $resultArray;
    }
}
