<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 31.10.18
 * Time: 22:55
 */

//use Signkeeper\Controllers;

require_once __DIR__ . '../../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => TRUE,
    ]
]);

$container = $app->getContainer();

$container['logger'] = function ($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(__DIR__ . "/../../logs/laba2/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

/*$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates', [
        'cache' => 'path/to/cache'
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};*/

$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('templates');
};

require_once __DIR__ . '/routes.php';
