<?php

//Turn on error reporting

ini_set('display_errors', 1);
error_reporting(E_ALL);

//Require autoload
require_once('vendor/autoload.php');

session_start();

$database = new Database();
$_SESSION['database'] = $database;
$dbh = $database->connect();

//Create an instance of the Base class
$f3 = Base::instance();

$f3->set('states', array("Alabama", "Alaska", "Arizona", "Arkansas",
    "California", "Colorado", "Connecticut", "Delaware", "Florida",
    "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas",
    "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts",
    "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana",
    "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico",
    "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma",
    "Oregon", "Pennsylvania", "Rhode Island", "South Carolina",
    "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia",
    "Washington", "West Virginia", "Wisconsin", "Wyoming"));

//Turn of Fat-Free error reporting
$f3->set('DEBUG', 3);

//Define a default route
$f3->route('GET /', function () {

    $_SESSION = array();
    $view = new View;
    echo $view->render('views/home.html');
});

//register route
$f3->route('GET|POST /register', function ($f3) {

    if (!empty($_POST)) {

        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];

        if (isset($_POST['premium'])) {
            $_SESSION['premium'] = true;
            $premiumMember = new PremiumMember($firstName, $lastName, $age, $gender, $phone);
            $_SESSION['member'] = $premiumMember;
            $f3->reroute('/profile');
        } else {
            $member = new Member($firstName, $lastName, $age, $gender, $phone);
            $_SESSION['member'] = $member;
            $f3->reroute('/profile');
        }
    }

    $template = new Template();
    echo $template->render('views/personal-information.html');
});

//profile route
$f3->route('GET|POST /profile', function ($f3) {

    if (!empty($_POST))
    {
        $member = $_SESSION['member'];

        $email = $_POST['email'];
        $member->setEmail($email);

        $state = $_POST['state'];
        $member->setState($state);

        $seeking = $_POST['seeking'];
        $member->setSeeking($seeking);

        $bib = $_POST['bib'];
        $member->setBio($bib);

        $_SESSION['member'] = $member;

        if ($_SESSION['premium'] == true)
        {
            $f3->reroute('/interests');
        }
        else
        {
            $f3->reroute('/summary');
        }
    }

    $template = new Template();
    echo $template->render('views/profile.html');
});

//interests route
$f3->route('GET|POST /interests', function ($f3) {

    $member = $_SESSION['member'];

    $inDoor = $_POST['inDoor'];
    $outDoor = $_POST['outDoor'];

    if (isset($inDoor) and isset($outDoor))
    {
        $member->setInDoorInterests($inDoor);
        $member->setOutDoorInterests($outDoor);
        $f3->reroute('/summary');
    }

    $_SESSION['member'] = $member;

    $template = new Template();
    echo $template->render('views/interests.html');
});

//summary route
$f3->route('GET|POST /summary', function ($f3) {

    $member = $_SESSION['member'];
    $database = $_SESSION['database'];

    $f3->set('firstName', $member->getFname());
    $f3->set('lastName', $member->getLname());
    $f3->set('gender', $member->getGender());
    $f3->set('age', $member->getAge());
    $f3->set('phone', $member->getPhone());
    $f3->set('email', $member->getEmail());
    $f3->set('state', $member->getState());
    $f3->set('seeking', $member->getSeeking());
    $f3->set('bib', $member->getBio());

    if ($_SESSION['premium'] == true)
    {
        $inInterests = implode(", ", $member->getInDoorInterests());
        $outInterests = ", " . implode(", ", $member->getOutDoorInterests());

        $f3->set('inInterests', $inInterests);
        $f3->set('outInterests', $outInterests);

        //inserting into database for premium
        $database->insertMember($member->getFname(), $member->getLname(), $member->getAge(), $member->getGender(),
            $member->getPhone(), $member->getEmail(), $member->getState(), $member->getSeeking(),
            $member->getBio(), 1, null, $inInterests . ", " . $outInterests);
    }
    else
    {
        $f3->set('inInterests', "");
        $f3->set('outInterests', "");

        //inserting into database for non premium
        $database->insertMember($member->getFname(), $member->getLname(), $member->getAge(), $member->getGender(),
            $member->getPhone(), $member->getEmail(), $member->getState(), $member->getSeeking(),
            $member->getBio(), 0, null, null);

    }
    $template = new Template();
    echo $template->render('views/summary.html');
});

//admin log in route
$f3->route('GET|POST /admin', function ($f3) {
    $view = new Template();
    echo $view->render('views/adminLogIn.html');

    if (($_POST['user']) == "admin" && $_POST['password'] == "admin" )  {
        $_SESSION['pass'] = true;
        $f3->reroute('/users');
    }
});

//users in route
$f3->route('GET|POST /users', function ($f3) {
    if ($_SESSION['pass'])
    {
        $database = $_SESSION['database'];
        $members = $database->getMembers();
        $f3->set('members', $members);
        $view = new Template();
        echo $view->render('views/admin.html');
        $_SESSION['pass'] = false;
    }
    else {
        $f3->reroute('/admin');

    }

});

//Display each member
$f3->route('GET|POST /member@memId', function($f3, $params) {
    $database = $_SESSION['database'];

    $member=$database->getMember($params['memId']);
    $f3->set('member', $member['0']);
    $view = new Template();
    echo $view->render('views/members.html');
});

//Run fat free
$f3->run();