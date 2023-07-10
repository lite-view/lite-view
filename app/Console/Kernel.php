<?php

namespace App\Console;


class Kernel
{
    public static function getCommand($argv)
    {
        // 加载命令
        foreach (glob(__DIR__ . '/Commands/*') as $file) {
            $arr = pathinfo($file);
            $class = '\\App\\Console\\Commands\\' . $arr['filename'];
            $cmd = new $class($argv);
            if (preg_match("/^{$cmd->signature}$/", $argv[1])) {
                return $cmd;
            }
        }
        return null;
    }

    // 打印列出所有命令
    public function list($argv)
    {
        foreach (glob(__DIR__ . '/Commands/*') as $file) {
            $arr = pathinfo($file);
            $class = '\\App\\Console\\Commands\\' . $arr['filename'];
            $cmd = new $class($argv);
            echo $cmd->signature, PHP_EOL;
        }
    }

    // 创建 jwt_secret app_key
    public function keyGenerate()
    {
        $string = file_get_contents(root_path('/config.json'));
        $arr = json_decode($string, true);
        $modify = false;
        if (empty($arr['jwt_secret'])) {
            $arr['jwt_secret'] = md5(date('YmdHis'));
            $modify = true;
        }
        if (empty($arr['app_key'])) {
            $arr['app_key'] = md5(date('YmdH:i:s'));
            $modify = true;
        }
        if ($modify) {
            file_put_contents(
                root_path('/config.json'),
                json_encode($arr, JSON_PRETTY_PRINT)
            );
        }
    }
}
