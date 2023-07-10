<?php

use Monolog\Logger;

return [
    "default" => [
        "Handler" => \Monolog\Handler\RotatingFileHandler::class,
        "path" => root_path("storage/logs/run.log"),
        "level" => Logger::INFO,
        "processors" => [
            \Monolog\Processor\MemoryUsageProcessor::class
        ]
    ]
];
