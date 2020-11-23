<?php


namespace App\Classes\Vendors;


use App\Models\Link;
use DiDom\Document;
use Illuminate\Mail\Mailer;

class OneMarka implements \App\Interfaces\ContentInterface
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
                $description = $document->first('#review::text');
                $images = $document->find('.product-item-detail-slider-image');
                foreach ($images as $image) {
                    $rawImageLink = $image->first('img')->getAttribute('src');
                    $linkImage[] = 'https://1marka.ru' . $rawImageLink;
                }
                $images = implode('#', $linkImage);
                $propertiesBlock = $document->find('#char > .psk135');
                foreach ($propertiesBlock as $propertyBlock) {
                    $property = $propertyBlock->first('.col-6 > .psk136::text');
                    $value = $propertyBlock->first('.col-6 > .psk137::text');
                    $property = str_replace(' ', '_', $property);
                    $property = trim(str_replace('.', '_', $property));
                    $combined[$property] = $value;
                }
                $combined['Название'] = $name;
                $combined['Описание'] = $description;
                $combined['Производитель'] = '1Marka';
                $combined['Изображения'] = $images;
                $resultArray[] = $combined;
            } catch (\Exception $e) {
                continue;
            }
        }
        return $resultArray;
    }
}
