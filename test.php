<?php


require_once __DIR__ . '/vendor/autoload.php';

// 加载配置文件
foreach (glob(__DIR__ . '/config/*') as $item) {
    $name = pathinfo($item)['filename'];
    $value = require_once $item;
    ToolMan::setCfg($name, $value);
}


//var_dump(root_path());
//
//var_dump(\App\Common\Validate::number(123.33333, ['tail' => 2]));
//var_dump(\App\Common\Validate::ascii('135。fasf'));


//echo  time() - strtotime(date('2023-01-01'));
//echo strtotime(date('2023-01-01'));

$r = str_split('abc', 3);
var_dump($r);
\LiteView\Aides\Log::error('aaaa');
