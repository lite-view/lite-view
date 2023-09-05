<?php

namespace App\Http;

use LiteView\Kernel\Route;
use LiteView\Kernel\Visitor;
use LiteView\Support\Dispatcher;

class Kernel
{
    public static function dispatch(Visitor $visitor)
    {
        try {
            $route = Route::current_route();
            list($action, $middleware) = array_values($route);
        } catch (\Throwable $e) {
            return new self('404 ' . $e->getMessage());
        }

        // 前置中间件
        foreach ($middleware as $one) {
            $class = '\\App\\Http\\Middleware\\' . $one;
            $error = Dispatcher::before($visitor, new $class);
            if ($error) {
                return new self($error);
            }
        }

        // 处理请求
        $response = Dispatcher::work($visitor, $action);

        // 后置中间件
        foreach ($middleware as $one) {
            $class = '\\App\\Http\\Middleware\\' . $one;
            Dispatcher::after($visitor, new $class, $response);
        }

        return new self($response);
    }

    private $data;

    private function __construct($data)
    {
        $this->data = $data;
    }

    // 请求响应
    public function response()
    {
        $rsp = $this->data;
        if (!is_null($rsp) && !is_bool($rsp)) {
            if (is_string($rsp) || is_numeric($rsp)) {
                echo $rsp;
            } else {
                echo json_encode($rsp, JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
