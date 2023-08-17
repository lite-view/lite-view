<?php

namespace App\Demo\Controllers;


use LiteView\Aides\Log;
use LiteView\Curl\Lite;
use LiteView\Kernel\Visitor;
use LiteView\Redis\RedisCli;
use LiteView\SQL\DB;

class DemoController
{
    public function db(Visitor $visitor)
    {
        $r = DB::crud()->select('users', 'id = 1')->one();
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
        lite_view('test/test.php', [], 'layout.php');
    }

    public function curl()
    {
        $r = Lite::request()->get('https://songcj.com/server_info.php');
        var_dump($r);
        $r = Lite::request()->post('https://songcj.com/server_info.php', []);
        var_dump($r);
    }
}