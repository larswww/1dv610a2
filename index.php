<?php
use view;
include("../config.php");

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/database.php');
require_once('controller/login.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// CREATE OBJECTS OF THE MODELS
$db = new \model\userDB($config);
//$db->saveUser("Admin", "Password");
//$db->getUser("Admin", "password");

// CREATE OBJECTS OF THE CONTROLLERS
$authController = new \Controller\AuthController();
$authController->setDb($db);

//CREATE OBJECTS OF THE VIEWS
$v = new \view\LoginView();
$v->setController($authController);

$dtv = new \view\DateTimeView();
$lv = new \view\LayoutView();

$lv->render($db->getIsLoggedIn(), $v, $dtv);

