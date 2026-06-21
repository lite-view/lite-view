<?php

namespace App\Http;

use LiteView\Exception\NotFoundException;
use LiteView\Kernel\View;
use LiteView\Kernel\Visitor;

class ExceptionManager extends \LiteView\Exception\ExceptionManager
{
    public bool $use = true;
    public Visitor $visitor;

    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    public function handle(array $msg, ?\Throwable $exception = null): bool
    {
        if ($exception instanceof NotFoundException) {
            http_response_code(404);
            echo View::renderTwig('404.twig');
            return true;
        }

        return false;
    }
}