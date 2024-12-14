<?php

use LiteView\Kernel\Route;

Route::get('/', function () {
    return lite_view('welcome.twig');
});


Route::get('hello/{name?}', [\App\Demo\Controllers\DemoController::class, 'hello'], ['SayHello']);
Route::get('db', 'App\Demo\Controllers\DemoController@db');
Route::post('log', 'App\Demo\Controllers\DemoController@log');

Route::group(['prefix' => 'group', 'middleware' => []], function () {
    Route::get('exception', function () {
        class X extends \LiteView\Support\ExceptionHandler
        {
            public function handle(array $e, \Throwable $exception = null)
            {
                echo '自定义异常处理';
            }
        }

        new X();

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
