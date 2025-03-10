<?php

namespace App\Common;

class ExceptionManager
{
    public $use = false;

    public function handle(array $msg, \Throwable $exception = null)
    {
        dump($msg);
    }
}