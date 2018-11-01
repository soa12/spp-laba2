<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 31.10.18
 * Time: 22:55
 */

require_once __DIR__ . '../../vendor/autoload.php';
require_once __DIR__ . '/db_config.php';;

use ImageUploadingService\Controllers\UserController;
use ImageUploadingService\Controllers\DocumentController;
use ImageUploadingService\Validation\Validator;
use \RedBeanPHP\R as R;

/** @var \RedBeanPHP\R */
R::setup(DBDRIVER . ':host=' . DBHOST . '; dbname=' . DBNAME, DBUSER, DBPASS);

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => TRUE
    ]
]);

/**
 * @var \Interop\Container\ContainerInterface $container
 */
$container = $app->getContainer();

$container['validator'] = function () {
    return new Validator;
};

$container['UserController'] = function () {
    return new UserController;
};

$container['ImageController'] = function () {
    return new ImageController;
};

$container['logger'] = function () {
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

$container['view'] = function () {
    return new \Slim\Views\PhpRenderer('templates');
};


/**
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param callable $next Next middleware
 * @return  \Psr\Http\Message\ResponseInterface
 */
$mwCheckJson = function ($request, $response, $next) {
    $parsedBody = $request->getBody();
    $data = json_decode($parsedBody);
    if (is_null($data)) {
        $answer = array('status' => '400', 'response' => 'Не JSON формат');
        return $response->withJson($answer);
    }
    return $response = $next($request, $response);
};

/**
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param callable $next Next middleware
 * @return  \Psr\Http\Message\ResponseInterface
 */
$mwCheckAuth = function ($request, $response, $next) {
    if (!$_SESSION['isLogin']) {
        $answer = array('status' => '400', 'response' => 'Please, sign in');
        return $response->withJson($answer);
    }
    return $response = $next($request, $response);
};

require_once __DIR__ . '/routes.php';
