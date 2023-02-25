# ZRouter


## Examples

```php
<?php

//Init ZRouter
$app = new ZRouter('https://localhost');

//Define controllers namespace
$app->namespace('Foo\\');

//get route
$app->get('/post/{id}', 'Bar:getPost');

//post route
$app->post('/post/add', 'Bar:addPost');

//delete route
$app->delete('/post/{id}', 'Bar:deletePost');

//put route
$app->put('/post/{id}', 'Bar:editPost');

$app->run();


if ($app->error) {
    //404, 405, 500
    $app->redirect('/error/' . $error['errcode']);
}

```


## Named Examples

```php
<?php

//Init ZRouter
$app = new ZRouter('https://localhost');

//Define controllers namespace
$app->namespace('Foo\\');

//get route
$app->get('/post/{id}', 'Bar:getPost', 'app.read');

//post route
$app->post('/post/add', 'Bar:addPost', 'app.add');

//delete route
$app->delete('/post/{id}', 'Bar:deletePost', 'app.delete');

//put route
$app->put('/post/{id}', 'Bar:editPost', 'app.put');

$app->run();


if ($app->error) {
    //404, 405, 500
    $app->redirect('/error/' . $error['errcode']);
}

```

## Middleware Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use ZRouter\ZRouter;

//Init ZRouter
$app = new ZRouter('https://localhost');

//Define controllers namespace
$app->namespace('Foo\\');

//get route
$app->get('/post/{id}', 'Bar:getPost', 'app.read', []);


//Define midlewares for the routes below
$app->middleware([
    \App\Middlewares\IsAuthenticated::class,
    \App\Middlewares\IsAuthenticated::class
    ]);

//post route
$app->post('/post/add', 'Bar:addPost', 'app.add');

//delete route
$app->delete('/post/{id}', 'Bar:deletePost', 'app.delete');

//put route
$app->put('/post/{id}', 'Bar:editPost', 'app.put');

//Unset middlewares
$app->middleware(null);

$app->get('/home', 'Bar:home');


$app->run();


if ($app->error) {
    //404, 405, 500
    $app->redirect('/error/' . $error['errcode']);
}

```


## Closure

```php
<?php

//Init ZRouter
$app = new ZRouter('https://localhost');

//Define controllers namespace
$app->namespace('Foo\\');

//get route
$app->get('/post/{id}', function(array $data){
    echo $data['id']; // {id} value
});

//run app
$app->run();


if ($app->error) {
    //404, 405, 500
    $app->redirect('/error/' . $error['errcode']);
}

```