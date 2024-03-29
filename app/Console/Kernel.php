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
        $commands = [
            'list' . str_repeat(' ', 50 - strlen('list')) . '# 打印列出所有命令',
            'init' . str_repeat(' ', 50 - strlen('init')) . '# 初始化配置信息',
            'version' . str_repeat(' ', 50 - strlen('version')) . '# 打印当前版本',
        ];
        foreach (glob(__DIR__ . '/Commands/*') as $file) {
            $arr = pathinfo($file);
            $class = '\\App\\Console\\Commands\\' . $arr['filename'];
            $cmd = new $class($argv);
            $commands[] = $cmd->signature . str_repeat(' ', 50 - strlen($cmd->signature)) . '# ' . $cmd->brief;
        }
        echo implode(PHP_EOL, $commands);
    }

    // 创建/初始化配置信息
    public function init()
    {
        if (!file_exists(root_path('/config.json'))) {
            copy(root_path('/config.default.json'), root_path('/config.json'));
        }
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

    public function version()
    {
        echo 'v0.1.9';
    }
}
