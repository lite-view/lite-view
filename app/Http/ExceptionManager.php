<?php

namespace App\Http;

class ExceptionManager
{
    public $use = false;

    public function handle(array $msg, \Throwable $exception = null)
    {
        dump($msg);
    }
}