<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => $_ENV['DB_DRIVER'],
    'database' => $_ENV['DB_DATABASE'],
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();