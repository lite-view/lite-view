<?php


const WORKING_DIR = __DIR__;


require_once __DIR__ . '/../vendor/autoload.php';


// 加载路由
foreach (glob(__DIR__ . '/../routes/*') as $item) {
    require_once $item;
}


// 加载配置文件
foreach (glob(__DIR__ . '/../config/*') as $item) {
    $name = pathinfo($item)['filename'];
    $value = require_once $item;
    \LiteView\Support\ToolMan::setCfg($name, $value);
}


$visitor = new \LiteView\Kernel\Visitor();
\App\Http\Kernel::dispatch($visitor)->response();

