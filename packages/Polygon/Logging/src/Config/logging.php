<?php

use Polygon\Logging\RequestLogger;

return [
    'driver' => 'custom',
    'via' => RequestLogger::class,
    'host' => env('LOGSTASH_HOST', '127.0.0.1'),
    'port' => env('LOGSTASH_PORT', 4718),
    'trace_level' => env('TRACE_LEVEL', 'debug'),
];
