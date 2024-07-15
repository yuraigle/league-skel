<?php

namespace App\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === Logger::class;
    }

    public function register(): void
    {
        $log       = new Logger('app');
        $handler   = new StreamHandler($_ENV['ROOT'] . $_ENV['LOG_PATH']);
        $formatter = new LineFormatter(null, "Y-m-d H:i:s", true, true);
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);

        $this->container->add(Logger::class, $log);
    }
}
