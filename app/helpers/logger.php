<?php

namespace Camagru\helpers;

use function Camagru\log_path;

/**
 * Class Logger
 * Helper class for logging messages to a file.
 */
class Logger
{
    /**
     * Log a message to the log file.
     *
     * @param string $message The message to log.
     */
    public static function log($message)
    {
        $logFilePath = log_path('app.log');

        if (file_exists($logFilePath)) {
            $logFile = fopen($logFilePath, 'a');
            if (!$logFile) {
                return;
            }
            fwrite($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL);
            fclose($logFile);
        } else {
            $logFile = fopen($logFilePath, 'w');
            if (!$logFile) {
                return;
            }
            fwrite($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL);
            fclose($logFile);
        }
    }
}
