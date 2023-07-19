<?php


require_once __DIR__ . '/vendor/autoload.php';

// 加载配置文件
foreach (glob(__DIR__ . '/config/*') as $item) {
    $name = pathinfo($item)['filename'];
    $value = require_once $item;
    ToolMan::setCfg($name, $value);
}


var_dump(root_path());
