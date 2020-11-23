<?php

namespace App\Http\Controllers;

use App\Classes\Vendors\DK;
use App\Classes\Vendors\Frap;
use App\Classes\Vendors\OneMarka;
use App\Classes\Vendors\Parly;
use App\Classes\Vendors\Terminus;
use App\Classes\Vendors\Vitra;
use App\Http\Requests\ContentRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContentController extends Controller
{
    public function getContent(ContentRequest $request)
    {
        $keyWords = $request->content;
        $loadContent = new Frap($keyWords);
//        $loadContent->parseContent($keyWords);
        $products = $loadContent->parseContent();
        foreach ($products as $product) {
            $brand = $product['Производитель'];
            if (DB::table('brands')->where('name', $brand)->doesntExist() && !is_null($brand)) {
                DB::table('brands')->insert(['name' => $brand]);
            }
            foreach ($product as $key => $value) {
                if (!Schema::hasColumn('contents', $key)) {
                    Schema::table('contents', function (Blueprint $table) use ($key) {
                        if ($key == 'АРТИКУЛ:') {
                            $table->string('АРТИКУЛ:');
                        } else {
                            $table->text($key)->nullable();
                        }
                    });
                }
            }
            DB::table('contents')->insertOrIgnore($product);
        }

        return redirect()->route('index');
    }
}
