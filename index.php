<?php

session_start();

//Turn on error reporting

ini_set('display_errors', 1);
error_reporting(E_ALL);

//Require autoload
require_once ('vendor/autoload.php');


//Create an instance of the Base class
$f3 = Base::instance();

//Turn of Fat-Free error reporting
$f3->set('DEBUG', 3);

//Validation file
require_once('model/validation-functions.php');


//Define a default route
$f3->route('GET /', function() {

    $view = new View;
    echo $view->render('views/home.html');
});

//register route
$f3->route('GET|POST /register', function($f3) {
    $_SESSION = array();
    $_SESSION['firstName'] = $_POST['firstName'];
    $_SESSION['lastName'] = $_POST['lastName'];
    $_SESSION['age'] = $_POST['age'];
    $_SESSION['gender'] = $_POST['gender'];
    $_SESSION['phone'] = $_POST['phone'];

    if (isset($_SESSION['firstName']))
    {
        $f3->reroute('/profile');
    }


//    if(validName($_POST['firstName']) && validName($_POST['lastName']) && validAge($_POST['age']) && validPhone($_POST['phone'])) { #if all inputs are valid
//        $_SESSION['firstName'] = $_POST['firstName'];
//        $_SESSION['lastName'] = $_POST['lastName'];
//        $_SESSION['age'] = $_POST['age'];
//        $_SESSION['phoneN'] = $_POST['phone'];
//        $_SESSION['gender'] = $_POST['gender'];
//        $f3->reroute('/profile');
//    }



    $template = new Template();
    echo $template->render('views/register.html');
});

//profile route
$f3->route('GET|POST /profile', function($f3) {
    $template = new Template();

    $_SESSION['email'] = $_POST['email'];
    $_SESSION['bib'] = $_POST['bib'];
    $_SESSION['state'] = $_POST['state'];
    $_SESSION['seeking'] = $_POST['seeking'];



    if (isset($_SESSION['email']))
    {
        $f3->reroute('/interests');
    }

    print_r($_SESSION);

    echo $template->render('views/profile.html');
});

//interests route
$f3->route('GET|POST /interests', function($f3) {
    $_SESSION['inDoor'] = $_POST['inDoor'];
    $_SESSION['outDoor'] = $_POST['outDoor'];


    if (isset($_SESSION['inDoor']) or isset($_SESSION['outDoor']))
    {
        $f3->reroute('/summary');
    }

    print_r($_SESSION);
    $template = new Template();
    echo $template->render('views/interests.html');
});

//summary route
$f3->route('GET|POST /summary', function() {

    $template = new Template();
    echo $template->render('views/summary.html');


});

//Run fat free
$f3->run();