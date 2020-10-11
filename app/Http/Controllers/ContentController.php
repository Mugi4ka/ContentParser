<?php

namespace App\Http\Controllers;

use App\Classes\BelBagno;
use App\Http\Requests\ContentRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContentController extends Controller
{
    public function getContent(ContentRequest $request)
    {
        $keyWords = $request->content;
        $loadContent = new BelBagno($keyWords);
//        $loadContent->parseContent($keyWords);
        $products = $loadContent->parseContent();
        foreach ($products as $product) {
            $vendor = $product['Производитель'];
            if (DB::table('vendors')->where('name', $vendor)->doesntExist() && !is_null($vendor)) {
                DB::table('vendors')->insert(['name' => $vendor]);
            }
            foreach ($product as $key => $value) {
                if (!Schema::hasColumn('contents', $key)) {
                    Schema::table('contents', function (Blueprint $table) use ($key) {
                        if ($key == 'Артикул') {
                            $table->string('Артикул')->unique();
                        } else {
                            $table->text($key)->nullable();
                        }
                    });
                }
            }
            DB::table('contents')->updateOrInsert(['Артикул' => $product['Артикул']], $product);
        }

        return redirect()->route('index');
    }
}
