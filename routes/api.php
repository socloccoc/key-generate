<?php

use Illuminate\Http\Request;

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

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('/checkKey', [
        'as' => 'key.check',
        'uses' => 'KeyApiController@checkKey'
    ]);

    Route::post('/updatePoint', [
        'as' => 'key.updataePoint',
        'uses' => 'KeyApiController@updatePoint'
    ]);

    Route::post('/addPointForKey', [
        'as' => 'key.addPointForKey',
        'uses' => 'KeyApiController@addPointForKey'
    ]);

    Route::get('/getPointByKey/{key}', [
        'as' => 'key.getPoint',
        'uses' => 'KeyApiController@getPointByKey'
    ]);

    Route::post('/validateKey', [
        'as' => 'key.validateKey',
        'uses' => 'KeyApiController@validateKey'
    ]);

    Route::resource('app', 'AppApiController');

});