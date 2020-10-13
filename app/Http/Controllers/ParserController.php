<?php

namespace App\Http\Controllers;

use App\Classes\SiteMap;
use App\Http\Requests\XmlLinkRequest;
use App\Models\Content;
use App\Models\Vendor;

class ParserController extends Controller
{
    public function index() {
        $vendorsList = Vendor::all();
        $contentList = Content::simplePaginate(10);
        return view('index', compact('vendorsList', 'contentList'));
    }

    public function getSiteMap(XmlLinkRequest $request)
    {
        $siteMap = new SiteMap();
        $siteMap->createSiteMap($request->sitemap);
        return redirect()->route('index');
    }
}
