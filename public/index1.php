<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

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

// $app = AppFactory::create();
// $app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

//ШАБЛОНЫ
// Обработчик 5
$app->get('/users/id/{id}', function ($request, $response, $args) {
    $params = [
        'id' => $args['id'],
        'nickname' => 'user-' . $args['id']
    ];

    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});

$courses = [
    ['id' => 4, 'name' => 'Sasha', 'phone' => 80291182929],
    ['id' => 2, 'name' => 'Pasha', 'phone' => 135],
    ['id' => 8, 'name' => 'Masha', 'phone' => 7788]
];

// Обработчик 6
// $app->get('/courses', function ($request, $response) use ($courses) {
//     $params = [
//         'courses' => $courses
//     ];

//     return $this->get('renderer')->render($response, 'courses/index.phtml', $params);
// });

//
//
$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];
// Обработчик 7
// $app->get('/users', function ($request, $response) use ($users) {
//     $term = $request->getQueryParam('term');
//     // $filteredCourses = /* filter courses by term */;
//     $filteredCourses = collect($users)->firstWhere('term', $term);

//     $params = ['users' => $filteredCourses];

//     // $params = [
//     //     'users' => $users
//     // ];

//     return $this->get('renderer')->render($response, 'users/index.phtml', $params);
// });


// Обработчик 8
$app->get('/courses', function ($request, $response) use ($users) {
    $messages = $this->get('flash')->getMessages();
    print_r($messages); // => ['success' => ['This is a message']]
    
    $term = $request->getQueryParam('term');
    // print_r($term);

    $filteredUsers = [];
    foreach ($users as $user) {
        // if (str_contains($user, $term)) {
        //     $filteredUsers[] = $user;
        // }
        if (mb_substr($user, 0, strlen($term)) === $term) {
            $filteredUsers[] = $user;
        }
    }

    // print_r($filteredUsers);

    $params = [
        'users' => $filteredUsers,
        'flash' => $messages
    ];

    $this->get('flash')->addMessage('success', 'This is a message');

    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});

//////////
$file = 'users.txt';
// Обработчик 9
$app->get('/users/new', function ($request, $response) {
    
    $params = [
        'user' => [
            'nickname' => '',
            'email' => ''
        ],
    ];

    return $this->get('renderer')->render($response, 'users/new.phtml', $params);
});

$app->post('/users', function ($request, $response) use ($file) {

    $user = $request->getParsedBodyParam('user');
    // print_r($user);

    
    $current = json_decode(file_get_contents($file));
    // print_r($current);

    $current[] = $user;
    // print_r($current);

    file_put_contents($file, json_encode($current));
    
    // return $response->withStatus();
        // return $response->withRedirect('/users', 302);

    // if (count($errors) === 0) {
    //     $repo->save($course);
    //     return $response->withRedirect('/courses', 302);
    // }

    $params = [
        'nickname' => '',
        'errors' => ''
    ];
    return $this->get('renderer')->render($response, "users/new.phtml", $params);
});


//1111111111111111111
// BEGIN (write your solution here)
// $app->get('/courses/new', function ($request, $response) {
//     $params = [
//         'course' => [
//             'paid' => '',
//             'title' => ''
//         ],
//         'errors' => []
//     ];
//     return $this->get('renderer')->render($response, 'courses/new.phtml', $params);
// });

// $app->post('/courses', function ($request, $response) use ($repo) {
//     $validator = new Validator();
//     $course = $request->getParsedBodyParam('course');
//     $errors = $validator->validate($course);

//     if (count($errors) === 0) {
//         $repo->save($course);
//         return $response->withRedirect('/courses', 302);
//     }

//     $params = [
//         'course' => $course,
//         'errors' => $errors
//     ];
//     return $this->get('renderer')->render($response, "courses/new.phtml", $params)->withStatus(422);
// });
// END
// class Validator implements ValidatorInterface
// {
//     public function validate(array $course)
//     {
//         // BEGIN (write your solution here)
//         $errors = [];
//         if (empty($course['paid'])) {
//             $errors['paid'] = "Can't be blank";
//         }

//         if (empty($course['title'])) {
//             $errors['title'] = "Can't be blank";
//         }

//         return $errors;
//         // END
//     }
// }
// <!-- BEGIN (write your solution here) -->
// <form action = "/courses" method = "post">
//   <div>
//     <label>
//         paid
//         <select name = "course[paid]">
//             <option value = ""></option>
//             <option <?= $course['paid'] === '1' ? 'selected' : '' > value = "1">Yes</option>
//             <option <?= $course['paid'] === '0' ? 'selected' : '' > value = "0">No</option>
//         </select>
//     </label>
//     <?php if (isset($errors['paid'])): >
//       <div><?= $errors['paid'] ></div>
//     <?php endif >
//   </div>
//   <div>
//     <label>
//         title
//       <input type = "text" required name = "course[title]" value = "<?= htmlspecialchars($course['title']) >">
//     </label>
//     <?php if (isset($errors['title'])): >
//       <div><?= $errors['title'] ></div>
//     <?php endif >
//   </div>
//   <input type = "submit" value = "Sign Up">
// </form>
// <!-- END -->


// Обработчик 1
$app->get('/', function ($request, $response) {
    $response->getBody()->write('Welcome to Slim!');
    return $response;
});

// Обработчик 2
// $app->get('/users', function ($request, $response) {
//     return $response->write('GET /userss');
// });

// Обработчик 3
// $app->post('/userss', function ($request, $response) {
//     return $response->withStatus(302);
// });

// Обработчик 4
$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});

$app->run();
