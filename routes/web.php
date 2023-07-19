<?php

use LiteView\Kernel\Route;


Route::get('/', function () {
    lite_view('welcome.php');
}, ['SayHello']);

Route::get('db', 'App\Demo\Controllers\DemoController@db');
Route::post('log', 'App\Demo\Controllers\DemoController@log');

Route::group(['prefix' => 'group', 'middleware' => []], function () {
    Route::get('exception', function () {
        throw new \Exception('my exception');
    });
    Route::get('error', function () {
        echo $no;
    });

    Route::group(['prefix' => 'test', 'middleware' => []], function () {
        Route::any('curl', 'App\Demo\Controllers\DemoController@curl');
        Route::any('render', 'App\Demo\Controllers\DemoController@render');
    });
});
