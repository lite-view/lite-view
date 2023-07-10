<?php


namespace App\Console\Commands;


use App\Console\Command;

class Hello extends Command
{
    public $signature = 'hello:(aa|bb|cc|abc)';

    public function handle()
    {
        list($nil, $fun) = explode(':', $this->args(1));
        echo 'hello ' . $fun;
        return 0;
    }
}
