<?php

namespace App\Http\Controllers;

use App\Classes\Sitemaps\FrapSiteMap;
use App\Classes\Sitemaps\TerminusSiteMap;
use App\Http\Requests\XmlLinkRequest;
use App\Jobs\LinkAfterCreateJob;
use App\Models\Brand;
use App\Models\Content;
use App\Models\Vendor;

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
        $siteMap = new FrapSiteMap();
        $siteMap->createSiteMap($request->sitemap);
//        LinkAfterCreateJob::dispatch($request->sitemap)->onConnection('database');

//        $siteMap->getLinks();
        return redirect()->route('index');
    }
}
