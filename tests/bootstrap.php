<?php

$loader = null;
$autoloadPaths = [
    __DIR__.'/../../../vendor/autoload.php',
    __DIR__.'/../vendor/autoload.php',
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        $loader = require $path;
        break;
    }
}

if (is_object($loader) && method_exists($loader, 'addPsr4')) {
    $loader->addPsr4('NotificationSystem\\', __DIR__.'/../src/');
    $loader->addPsr4('NotificationSystem\\Tests\\', __DIR__.'/');
}

require_once __DIR__.'/TestCase.php';
