<?php


namespace App\Interfaces;


use App\Http\Requests\XmlLinkRequest;

interface SiteMapInterface
{
    public function createSiteMap($request);
}
