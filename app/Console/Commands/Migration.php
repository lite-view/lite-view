<?php


namespace App\Console\Commands;


use App\Console\Command;
use LiteView\SQL\Connect;


class Migration extends Command
{
    public $signature = 'migration:(export|initialize|sync)';
    public $brief = '数据迁移';

    public function handle()
    {
        list($nil, $fun) = explode(':', $this->args(1));
        $this->$fun();
    }

    // 导出表结构
    public function export()
    {
        $current = date('Y_m_d_His_');
        $tables = Connect::db()->query('show tables')->fetchAll();
        foreach ($tables as $one) {
            $table = end($one);
            $name = Connect::db()->query("show create table `$table`")->fetchColumn(0);
            $sql = Connect::db()->query("show create table `$table`")->fetchColumn(1);
            $path = root_path('database/migrations/') . $current . $name . '.sql';
            file_put_contents($path, $sql);
        }
        sleep(1);
        echo '导出成功';
    }

    // 导入表结构
    public function initialize()
    {
        $data = $this->tables();
        $tables = $data[count($data) + $this->args(2, -1)];
        foreach ($tables as $file) {
            $table_name = substr($file, 18, -4);
            try {
                Connect::db()->query("desc {$table_name}")->fetchColumn();
                //已存在
                echo '已存在' . $table_name . PHP_EOL;
                continue;
            } catch (\Exception $e) {

            }

            $sql = file_get_contents(root_path('database/migrations/' . $file));
            Connect::db()->exec($sql);
            echo 'ok ' . $table_name . PHP_EOL;
        }
    }

    // 同步备分的表结构，只能新增
    public function sync()
    {
        $table = $this->args(2);
        $data = $this->tables(true);
        $dt = $data[count($data) + $this->args(3, -1)];
        $tmp_table = $dt . '_' . $table;
        $sql = file_get_contents(root_path('database/migrations/' . $tmp_table . '.sql'));
        Connect::db()->exec(str_replace("`$table`", "`$tmp_table`", $sql));

        $old = Connect::db()->query("desc $table")->fetchAll();
        $new = Connect::db()->query("desc $tmp_table")->fetchAll();

        $old = array_column($old, NULL, 'Field');
        $new = array_column($new, NULL, 'Field');

        foreach ($new as $key => $value) {
            list($Field, $Type, $Null, $Key, $Default, $Extra) = array_values($value);
            $Null = 'YES' == $Null ? 'NULL' : 'NOT NULL';
            $Default = $Default ? $Default : 'NULL';
            if (isset($old[$key])) {
                if ($value != $old[$key]) {
                    //字段存在但不一至
                    Connect::db()->exec("ALTER TABLE `$table` MODIFY COLUMN `$Field` $Type $Null DEFAULT $Default");
                }
            } else {
                //字段不存在，直接新增
                Connect::db()->exec("ALTER TABLE `$table` ADD COLUMN `$Field` $Type $Null DEFAULT $Default");
            }
        }
        Connect::db()->exec("drop table $tmp_table");
        echo '同步成功';
    }

    private function tables($keys = false)
    {
        $arr = scandir(root_path('database/migrations/'));
        $data = [];
        foreach ($arr as $file) {
            if ('.' == $file || '..' === $file) {
                continue;
            }
            $data[substr($file, 0, 17)][] = $file;
        }
        if ($keys) {
            return array_keys($data);
        }
        return array_values($data);
    }
}
