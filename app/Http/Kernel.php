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

        // 全局前置中间件
        foreach (self::$middleware as $class) {
            $errMsg = Dispatcher::before($visitor, new $class);
            if ($errMsg) {
                return new self($errMsg);
            }
        }

        // 前置中间件
        foreach ($target['middleware'] as $one) {
            $class  = '\\App\\Http\\Middleware\\' . $one;
            $errMsg = Dispatcher::before($visitor, new $class);
            if ($errMsg) {
                return new self($errMsg);
            }
        }

        // 处理请求
        $response = Dispatcher::work($target, $params, $visitor);

        // 后置中间件
        foreach ($target['middleware'] as $one) {
            $class = '\\App\\Http\\Middleware\\' . $one;
            Dispatcher::after($visitor, new $class, $response);
        }

        // 全局后置中间件
        foreach (self::$middleware as $class) {
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
