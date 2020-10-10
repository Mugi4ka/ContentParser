<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\ParserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ParserController::class, 'index'])->name('index');
Route::post('getSiteMap', [ParserController::class, 'getSiteMap'])->name('get-site-map');
Route::post('getContent', [ContentController::class, 'getContent'])->name('get-content');
