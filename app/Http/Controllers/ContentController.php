<?php

namespace App\Http\Controllers;

use App\Classes\BelBagno;
use App\Http\Requests\ContentRequest;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function getContent(ContentRequest $request)
    {
        $keyWords = $request->content;
        $loadContent = new BelBagno();
        $loadContent->parseContent($keyWords);
        return redirect()->route('index');
    }
}
