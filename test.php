<?php
session_start();

include("../config.php");

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('controller/AuthController.php');

//require_once('model/UserDatabase.php');
//require_once('controller/AuthController.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// CREATE OBJECTS OF THE MODELS

// CREATE OBJECTS OF THE CONTROLLERS
//$authController = new \Controller\AuthController();
//$authController->setDb($db);
//$authController->router();


try {

    //CREATE OBJECTS OF THE VIEWS
    $gk = new \model\GateKeeper();
    $v = new \view\LoginView();
    $v->setGateKeeper($gk);

//$v->setController($authController);
    $dtv = new \view\DateTimeView();
    $lv = new \view\LayoutView();

    $db = new \model\UserDatabase($config);
    $gk->connectDatabase($db);


//$test = new \view\RegisterView();

    $c = new \controller\AuthController();
    $c->setViewAndKeeper($v, $gk);
    $v->setController($c);
    $v->getUserInput();
    $v->response();

    $lv->render($gk, $v, $dtv);
//
    //TODO change to a spec type of error catching.
} catch (Exception $e) {
    $v->handleError($e->getMessage());
    $v->response();
    $lv->render($gk, $v, $dtv);

}

//CREATE OBJECTS OF THE VIEWS
//$v = new \view\LoginView();
////$v->setController($authController);
//$dtv = new \view\DateTimeView();
//$lv = new \view\LayoutView();
//$gk = new \model\GateKeeper();
//
////$test = new \view\RegisterView();
//
//$c = new \controller\AuthController();
//$c->setViewAndKeeper($v, $gk);
//$v->setGateKeeper($gk);
//$v->setController($c);
//$lv->render($gk->getIsLoggedIn(), $v, $dtv);
//


