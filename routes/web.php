<?php

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

Route::get('/', function () {
    return Redirect::to('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth'], function() {
    Route::resource('app', 'AppController');

    Route::resource('key', 'KeyController');

    Route::get('deleteKey/{id}', [
        'as'   => 'key.deleteKey',
        'uses' => 'KeyController@deleteKey'
    ]);

    Route::post('updateExpireDate', [
        'as'   => 'key.updateExpireDate',
        'uses' => 'KeyController@updateExpireDate'
    ]);
});
Route::match(['get', 'post'], 'register', function(){
    return redirect('/login');
});

Route::match(['get', 'post'], 'password/reset', function(){
    return redirect('/login');
});

Route::match(['get', 'post'], 'password/email', function(){
    return redirect('/login');
});

Route::match(['get', 'post'], 'password/reset/{token}', function(){
    return redirect('/login');
});

Route::match(['get', 'post'], 'password/reset', function(){
    return redirect('/login');
});

Route::group(['prefix' => 'ajax'], function () {
    Route::post('getKeyInfo', [
        'as' => 'getKeyInfo',
        'uses' => 'AjaxController@getKeyInfo'
    ]);
});