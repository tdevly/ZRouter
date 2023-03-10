# ZRouter


## Setup 
### Composer installation
```php
composer require tdevly/zrouter:0.0.1-alpha
```

### .htaccess 
```htaccess
RewriteEngine On
Options All -Indexes

# ROUTER URL Rewrite
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]

```

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
$app->get('/post/{id}', 'Bar:getPost', 'app.read');


//Define midlewares for the routes below
$app->middleware([
    \App\Middlewares\IsAuthenticated::class,
    \App\Middlewares\ExampleMiddleware::class
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



## Middleware class

```php
<?php

namespace Foo\Middlewares;

use ZRouter\Middleware;
use ZRouter\ZRouter;

class IsAuthenticated implements Middleware
{
    public function handle(): bool
    {
        return true; //No redirect
    }

    public function callback(ZRouter $zrouter)
    {
        //this method execute if handle method return false
    }
}

```