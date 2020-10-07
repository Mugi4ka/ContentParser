<?php

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
Route::get('getLinks', [ParserController::class, 'getLinks'])->name('get-links');
Route::post('getSiteMap', [ParserController::class, 'getSiteMap'])->name('get-site-map');
Route::post('getContent', [ParserController::class, 'getContent'])->name('get-content');
Route::get('getAllProperties', [ParserController::class, 'getAllProperties']);
