<?php


require_once __DIR__ . '/vendor/autoload.php';

// 加载配置文件
foreach (glob(__DIR__ . '/../config/*') as $item) {
    $name = pathinfo($item)['filename'];
    $value = require_once $item;
    ToolMan::setCfg($name, $value);
}


// 根据环境加载配置
$env_config = root_path() . 'config.' . cfg('app_env') . '.json';
if (file_exists($env_config)) {
    $string = file_get_contents($env_config);
    $config = json_decode($string, true);
    foreach ($config as $name => $value) {
        ToolMan::setCfg($name, $value);
    }
}


var_dump(root_path());
var_dump(cfg('app_name1'));