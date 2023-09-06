<?php

namespace App\Http\Middleware;


class SayHello
{
    public function handle()
    {
        echo '<div style="text-align: center;height: 100px;">hello LiteView</div>';
        return 0;
    }

    public function after()
    {
        echo '<div style="text-align: center;height: 100px;">处理完成后！</div>';
    }
}
