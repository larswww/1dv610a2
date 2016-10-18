<?php
session_start();

include("../config.php");

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');

//require_once('model/UserDatabase.php');
//require_once('controller/AuthController.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// CREATE OBJECTS OF THE MODELS
//$db = new \model\userDB($config);

// CREATE OBJECTS OF THE CONTROLLERS
//$authController = new \Controller\AuthController();
//$authController->setDb($db);
//$authController->router();

//CREATE OBJECTS OF THE VIEWS
$v = new \view\LoginView();
//$v->setController($authController);
$dtv = new \view\DateTimeView();
$lv = new \view\LayoutView();
$gk = new \model\GateKeeper();

//$test = new \view\RegisterView();

$lv->render($gk->getIsLoggedIn(), $v, $dtv);

