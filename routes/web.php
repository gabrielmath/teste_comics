<?php

use Illuminate\Support\Facades\Route;
use App\Services\Comics\MarvelAccess;
use App\Services\Comics\MarvelData;

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

$comics = new MarvelData(new MarvelAccess());

Route::get('/', function () use ($comics) {

    return view('welcome', compact('comics'));
});

Route::get('/data-json', function () use ($comics) {
    return $comics->arrayListComics();
});
