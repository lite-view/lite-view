<?php

namespace App\Http\Middleware;


use LiteView\Kernel\Visitor;

class SayHello
{
    public function handle(Visitor $visitor, \Closure $next)
    {
        echo '<div style="text-align: center;height: 100px;">控制器运行之前</div>';
        $response = $next($visitor);
        echo '<div style="text-align: center;height: 100px;">控制器运行之后</div>';
        return $response;
    }
}
