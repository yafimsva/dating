<?php

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Require autoload
require_once ('vendor/autoload.php');

//Create an instance of the Base class
$f3 = Base::instance();

//Turn of Fat-Free error reporting
$f3->set('DEBUG', 3);

//Define a default route
$f3->route('GET /', function() {

    $view = new View;
    echo $view->render('views/home.html');
});

$f3->route('GET /register', function() {
    $view = new View;
    echo $view->render('views/register.html');
});

//Run fat free
$f3->run();