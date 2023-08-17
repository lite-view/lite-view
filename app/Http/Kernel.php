<?php

namespace App\Http;

use LiteView\Kernel\Route;
use LiteView\Kernel\Visitor;

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
            $mid = new $class();
            if (method_exists($mid, 'handle')) {
                $error = $mid->handle($visitor);
                if ($error) {
                    return new self($error);
                }
            }
        }

        // 请求处理
        if (is_callable($action)) {
            if (is_array($action)) {
                list($class, $action) = $action;
                $response = (new $class($visitor))->$action($visitor);
            } else {
                $response = $action($visitor);
            }
        } else {
            list($class, $action) = explode('@', $action);
            $response = (new $class($visitor))->$action($visitor);
        }

        // 后置中间件
        foreach ($middleware as $one) {
            $class = '\\App\\Http\\Middleware\\' . $one;
            $mid = new $class();
            if (method_exists($mid, 'after')) {
                $mid->after($visitor, $response);
            }
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
