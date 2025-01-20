<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

$resetDatabase = filter_var($_ENV['BOOTSTRAP_RESET_DATABASE'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
if (true === $resetDatabase) {
    echo 'Resetting test database...';
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:schema:drop --env=test --force --no-interaction',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:schema:update --env=test --force --no-interaction',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:fixtures:load --env=test --no-interaction',
        __DIR__
    ));
    echo ' Done'.PHP_EOL.PHP_EOL;
}

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
