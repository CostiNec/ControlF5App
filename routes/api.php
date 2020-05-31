<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/get_links','SeedController@actionAsync');
Route::post('/insert_results','SeedController@actionAsyncArticle');
Route::post('/get_suggestions_info','SearchController@getSuggestions');
Route::post('/get_results_info','SearchController@getResults');
Route::post('/more_info','SeedController@parseMoreInfo');
