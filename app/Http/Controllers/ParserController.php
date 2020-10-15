<?php

namespace App\Http\Controllers;

use App\Classes\Sitemaps\BelbagnoSiteMap;
use App\Classes\Sitemaps\RMSSiteMap;
use App\Http\Requests\XmlLinkRequest;
use App\Models\Brand;
use App\Models\Content;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    public function index() {
        $brandsList = Brand::all();
        $vendorsList = Vendor::all();
        $contentList = Content::simplePaginate(10);
        return view('index', compact('brandsList', 'contentList', 'vendorsList'));
    }

    public function getSiteMap(XmlLinkRequest $request)
    {
        $siteMap = new RMSSiteMap();
        $siteMap->getLinks();
//        $siteMap->createSiteMap($request->sitemap);
//        return redirect()->route('index');
    }
}
