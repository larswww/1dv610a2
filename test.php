<?php
session_start();

// config file returning array $config with credentials for database
include("../config.php");

require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('controller/AuthController.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

try {

    $gk = new \model\GateKeeper();
    $v = new \view\LoginView();
    $v->setGateKeeper($gk);

    $dtv = new \view\DateTimeView();
    $lv = new \view\LayoutView();

    $db = new \model\UserDatabase($config);
    $gk->connectDatabase($db);

    $c = new \controller\AuthController();
    $c->setViewAndKeeper($v, $gk);

    $v->setController($c);
    $v->getUserInput();
    $v->response();

    $lv->render($gk, $v, $dtv);

    // custom exception class for all exceptions related to this module
} catch (AuthenticationException $e) {
    $v->handleError($e->getMessage());
    $v->response();
    $lv->render($gk, $v, $dtv);

} catch (Exception $e) {
    $v->handleError("Problem with Database, try later");
    error_log($e->getMessage(), 0);

}


