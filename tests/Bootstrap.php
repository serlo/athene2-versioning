<?php

$moduleName = 'Versioning';

if ($moduleName === 'insert modulename here') {
    throw new RuntimeException('Please define the name of the module!');
}

use CommonTest\Util\ServiceManagerFactory;

ini_set('error_reporting', E_ALL);

$files = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $loader = require $file;

        break;
    }
}

if (!isset($loader)) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you install via composer?');
}

$loader->add('CommonTest\\', __DIR__);
$loader->add($moduleName . 'Test\\', __DIR__);

$configFiles = [
    __DIR__ . '/TestConfiguration.php',
    __DIR__ . '/TestConfiguration.php.dist'
];

foreach ($configFiles as $configFile) {
    if (file_exists($configFile)) {
        $config = require $configFile;

        break;
    }
}

ServiceManagerFactory::setApplicationConfig($config);
ServiceManagerFactory::getServiceManager();
unset($moduleName, $files, $file, $loader, $configFiles, $configFile, $config);
