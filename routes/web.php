<?php

use LiteView\Kernel\Route;


Route::get('/', function () {
    lite_view('welcome.php');
}, ['SayHello']);

Route::get('db', 'App\Test\Controllers\TestController@db');
Route::post('log', 'App\Test\Controllers\TestController@log');

Route::group(['prefix' => 'group', 'middleware' => []], function () {
    Route::get('exception', function () {
        throw new \Exception('my exception');
    });
    Route::get('error', function () {
        echo $no;
    });

    Route::group(['prefix' => 'test', 'middleware' => []], function () {
        Route::any('curl', 'App\Test\Controllers\TestController@curl');
        Route::any('render', 'App\Test\Controllers\TestController@render');
    });
});
