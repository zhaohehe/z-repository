<?php
/*
 * Sometime too hot the eye of heaven shines
 */

require 'vendor/autoload.php';

$capsule = new Illuminate\Database\Capsule\Manager();

$dbConfig = [
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'poem',
	'username' => 'root',
	'password' => '',
	'charset' => 'utf8',
	'collection' => 'utf8_unicode_ci'
];

$capsule->addConnection($dbConfig);
$capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container()));

$capsule->setAsGlobal();

$capsule->bootEloquent();