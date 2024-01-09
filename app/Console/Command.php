<?php


namespace App\Console;


use LiteView\Kernel\Visitor;

class Command
{
    public $signature; //以正则方式匹配
    public $brief; //功能简介
    public $arguments; //参数 $argv


    public function __construct($params = [])
    {
        $this->arguments = $params;
    }

    public function args($index, $default = null)
    {
        if (isset($this->arguments[$index])) {
            return $this->arguments[$index];
        }
        return $default;
    }
}
