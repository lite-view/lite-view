<?php

use Monolog\Logger;

return [
    "default" => [
        "handlers" => [
            new \Monolog\Handler\RotatingFileHandler(root_path("storage/logs/run.log"), Logger::INFO),
            new \Monolog\Handler\StreamHandler('php://stdout', Logger::INFO),  // php://stdout OR php://stderr
        ],
        "format" => null,
        "processors" => [
            \Monolog\Processor\MemoryUsageProcessor::class
        ]
    ]
];
