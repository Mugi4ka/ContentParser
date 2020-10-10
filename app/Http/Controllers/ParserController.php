<?php

namespace App\Http\Controllers;

use App\Classes\SiteMap;
use App\Http\Requests\XmlLinkRequest;
use App\Models\Content;
use App\Models\Vendor;

class ParserController extends Controller
{
    public function index() {
        $vendorsList = Vendor::all()->sortBy('name');
        return view('index', compact('vendorsList'));
    }

    public function getSiteMap(XmlLinkRequest $request)
    {
        $siteMap = new SiteMap();
        $siteMap->createSiteMap($request->sitemap);
        return redirect()->route('index');
    }

}
