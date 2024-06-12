<?php

namespace Camagru\helpers;

use function Camagru\log_path;

class Logger {
    public static function log($message) {
        if (file_exists(log_path('app.log'))) {
            $logFile = fopen(log_path('app.log'), 'a');
            if (!$logFile) {
                return;
            }
            fwrite($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL);
            fclose($logFile);
        }
    }
}