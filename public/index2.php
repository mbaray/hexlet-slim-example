<?php

use DI\Container;
use Slim\Factory\AppFactory;

// Старт PHP сессии
session_start();

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/foo', function ($req, $res) {
    // Добавление флеш-сообщения. Оно станет доступным на следующий HTTP-запрос.
    // 'success' — тип флеш-сообщения. Используется при выводе для форматирования.
    // Например можно ввести тип success и отражать его зелёным цветом (на Хекслете такого много)
    $this->get('flash')->addMessage('success', 'This is a message');

    return $res->withRedirect('/bar');
});

$app->get('/bar', function ($req, $res) {
    // Извлечение flash сообщений установленных на предыдущем запросе
    $messages = $this->get('flash')->getMessages();
    print_r($messages); // => ['success' => ['This is a message']]

    $params = ['flash' => $messages];
    return $this->get('renderer')->render($res, 'bar.phtml', $params);
});

$app->run();
