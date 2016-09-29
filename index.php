<?php
session_start();
session_regenerate_id();
setcookie("PHPSESSID", $_SESSION["PHPSESSID"], time() + (60 * 60 * 0.5), "/a2");


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

// CREATE OBJECTS OF THE CONTROLLERS
$authController = new \Controller\AuthController();
$authController->setDb($db);

//CREATE OBJECTS OF THE VIEWS
$v = new \view\LoginView();
$v->setController($authController);

$dtv = new \view\DateTimeView();
$lv = new \view\LayoutView();
//$test = new \view\RegisterView();

$lv->render($db->getIsLoggedIn(), $v, $dtv);

