<?php

require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => $_ENV['DB_DRIVER'],
    'database' => $_ENV['DB_DATABASE'],
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::schema()->dropIfExists('users');
Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('username');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('groups');
Capsule::schema()->create('groups', function ($table) {
    $table->increments('id');
    $table->string('name');
    $table->unsignedInteger('creator_id');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('group_members');
Capsule::schema()->create('group_members', function ($table) {
    $table->increments('id');
    $table->unsignedInteger('group_id');
    $table->unsignedInteger('user_id');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('messages');
Capsule::schema()->create('messages', function ($table) {
    $table->increments('id');
    $table->unsignedInteger('group_id');
    $table->unsignedInteger('user_id');
    $table->text('message');
    $table->timestamps();
});

echo "Migration complete! ✅\n";