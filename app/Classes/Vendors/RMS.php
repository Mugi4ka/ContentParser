<?php


namespace App\Classes\Vendors;

use App\Interfaces\ContentInterface;
use App\Models\Link;
use DiDom\Document;


class RMS implements ContentInterface
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
            $linkImage = [];
            $document = new Document($neededLink, true);
            $title = $document->first('.h1-title::text');
            $images = $document->find('a.full-image');
            $sku = $document->first('*[^data-=ARTIKUL] > span.value::text');
            foreach ($images as $image) {
                $rawImageLink = $image->getAttribute('href');
                $linkImage[] = 'https://gutsant.ru' . $rawImageLink;
            }
            $images = implode('#', $linkImage);
            $combined['Изображения'] = $images;
            $combined['Название'] = $title;
            $combined['Артикул'] = $sku;
            $combined['Производитель'] = "Blanco";
            $resultArray[] = $combined;
        }
        return $resultArray;
    }
}
