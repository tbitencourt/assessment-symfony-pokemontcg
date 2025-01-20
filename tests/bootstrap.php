<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// executes the "php bin/console --env=test doctrine:schema:update" command
passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" doctrine:schema:update',
    $_ENV['APP_ENV'],
    __DIR__
));

// executes the "php bin/console --env=test doctrine:fixtures:load" command
// passthru(sprintf(
//     'APP_ENV=%s php "%s/../bin/console" doctrine:fixtures:load',
//     $_ENV['APP_ENV'],
//     __DIR__
// ));
