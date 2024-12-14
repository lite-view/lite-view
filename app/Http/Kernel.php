<?php

namespace App\Http;

use LiteView\Kernel\Route;
use LiteView\Kernel\Visitor;
use LiteView\Support\Dispatcher;

class Kernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected static $middleware = [

    ];


    public static function dispatch(Visitor $visitor): Kernel
    {
        list($target, $params) = Route::match();
        if (empty($target)) {
            header("HTTP/1.1 404");
            return new self('route not found');
        }

        $middleware = [];
        // 全局中间件
        foreach (self::$middleware as $class) {
            $middleware[] = $class;
        }
        // 组装路由中间件
        foreach ($target['middleware'] as $class) {
            if (!class_exists($class)) {
                $class = '\\App\\Http\\Middleware\\' . $class;
            }
            $middleware[] = $class;
        }
        $target['middleware'] = $middleware;

        // 处理请求
        $args = [];
        if (is_array($params)) {
            foreach ($params as $param) {
                if ('' !== $param) $args[] = $param;
            }
        }
        $response = Dispatcher::work($target, $args, $visitor);
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
