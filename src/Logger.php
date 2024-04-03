<?php

namespace src\Core;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{

    private string $errorPath = './../Storage/Logs/errors.txt';
    private string $infoPath = './../Storage/Logs/info.txt';

    public function emergency($message, array $context = []): void
    {
//        $this->log(LogLevel::EMERGENCY, $message, $context);
    }


    public function alert($message, array $context = []): void
    {
//        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
//        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
//        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
//        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
//        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $contextString = implode("\n", $context);
        $message = sprintf('[%s] %s: %s%s', date('Y-m-d H:i:s'), $level, $message . $contextString . "\n", PHP_EOL);

        if ($level === LogLevel::ERROR) {
            file_put_contents($this->errorPath, $message, FILE_APPEND);
        } elseif ($level === LogLevel::INFO) {
            file_put_contents($this->infoPath, $message, FILE_APPEND);
        }
    }
}