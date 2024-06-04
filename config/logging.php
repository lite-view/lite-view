<?php

use Monolog\Logger;

return [
    "default" => [
        "format" => null, // reference \LiteView\Utils\Log@lineFormatter
        "handlers" => [
            new \Monolog\Handler\RotatingFileHandler(root_path("storage/logs/run.log"), 7, Logger::INFO),
            new \Monolog\Handler\StreamHandler('php://stdout', Logger::INFO),  // php://stdout OR php://stderr
        ],
        "processors" => [
            \Monolog\Processor\MemoryUsageProcessor::class
        ]
    ]
];
