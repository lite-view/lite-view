<?php

namespace App\Demo\Controllers;


use LiteView\Curl\Lite;
use LiteView\Kernel\Visitor;
use LiteView\Redis\RedisCli;
use LiteView\SQL\Crud;
use LiteView\Utils\Log;


class DemoController
{
    public function hello(Visitor $visitor)
    {
        $name = $visitor->get('name', 'lite-view');
        return ['name' => $name];
    }

    public function db(Visitor $visitor)
    {
        $r = Crud::db()->select('users', 'id > 0')->prep()->one();
        var_dump($r);
        RedisCli::select()->set('a', time(), 60);
        $r = RedisCli::select()->get('a');
        var_dump($r);
    }

    public function log(Visitor $visitor)
    {
        $i = $visitor->input();
        Log::info(json_encode($i));
        return ['message' => 'ok'];
    }

    public function render()
    {
        return lite_view('test/test.twig', ['t' => time()]);
    }

    public function curl()
    {
        $r = Lite::request()->get('https://songcj.com/server_info.php');
        var_dump($r);
        $r = Lite::request()->post('https://songcj.com/server_info.php', []);
        var_dump($r);
    }
}
