<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

AppFactory::setContainer($container);
$app = AppFactory::create();
// $app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$file = 'users.txt';

// $app->get('/users/new', function ($request, $response) {
    
//     $params = [
//         'user' => [
//             'nickname' => '',
//             'email' => ''
//         ],
//     ];

//     return $this->get('renderer')->render($response, 'users/new.phtml', $params);
// });

$app->get('/schools', function ($request, $response) use ($file) {
    
    $users = json_decode(file_get_contents($file), true);
    $schools = collect($users)->all();

    $params = [
        'schools' => $schools
    ];

    return $this->get('renderer')->render($response, "schools/index.phtml", $params);
})->setName('schools');


$app->get('/schools/{id}', function ($request, $response, array $args) use ($file) {
    $id = $args['id'];

    $users = json_decode(file_get_contents($file), true);
    $school = collect($users)->find($id);

    $params = [
        'school' => $school
    ];

    return $this->get('renderer')->render($response, 'school/show.phtml', $params);
})->setName('school');



$app->get('/companies/{id:[0-9]+}', function ($request, $response, $args) {
    $id = $args['id'];

    $companies = [
        ['id' => 4, 'name' => 'Sasha', 'phone' => 80291182929],
        ['id' => 2, 'name' => 'Pasha', 'phone' => 135],
        ['id' => 8, 'name' => 'Masha', 'phone' => 7788]
    ];

    
    $company = collect($companies)->firstWhere('id', $id);
    print_r(collect($companies));

    if (!$company) {
        return $response->withStatus(404)->write('Page not found');
    }
    return $response->write(json_encode($company));
});

$app->run();
