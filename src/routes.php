<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 31.10.18
 * Time: 22:56
 */

$app->get('/', function ($request, $response) {
    $this->logger->addInfo('Начальная страница');
    $templateVariables = array('startLabel' => 'Приложение запущено');
    $response = $this->view->render($response, 'app.phtml', ['variables' => $templateVariables]);
    return $response;
});

$app->get('/action', function ($request, $response) {
    $params = $request->getQueryParams();
    $this->logger->addInfo('Нажата кнопка ' . $params['submit']);
    $templateVariables = array('startLabel' => $params['submit']);
    $response = $this->view->render($response, 'app.phtml', ['variables' => $templateVariables]);
    return $response;
});

$app->get('/logs', function ($request, $response) {
    $this->logger->addInfo('Просмотр логов');
    $logs = file(__DIR__ .'/../../logs/app.log');
    $response = $this->view->render($response, 'logs.phtml', ['variables' => $logs]);
    return $response;
});

$app->get('/start', function ($request, $response) {
    echo 'hello world';
});