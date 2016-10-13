<?php
namespace Controller;
use view;

class AuthController {
    private $viewAction;

    private function setViewAction(string $action){
        $this->viewAction = $action;
    }

    public function getViewAction(){
        return $this->viewAction;
    }

    public function __construct()
    {
        $this->action = (!empty($_GET) && isset($_GET['action'])) ? $_GET['action'] : 'defaultView';
        $this->noIncomingParams = (empty($_POST)) ? true : false;

        foreach ($_POST as $key => $value) {
            $fieldNameOnly = explode("::", $key);
            $formActionName = $fieldNameOnly[1];
            $this->$formActionName = $value;
        }
    }



    function router() {

        try {
            $authAction = $this->action;
            $viewAction = $this->$authAction();

        } catch (\Exception $e) {

        }

        // router calls a controller function, which then calls the right function in LoginView, which sets all the messages.
        // and passes in a $message to said view function if need be.
        // should return a function name instead, and message can then be set in the view.

        // the view functions:
        // loggedIn(); "Welcome" or "Welcome and you will be remembered
        // loggedInWithCookies();
        // router should call the right function in LoginView, which sets all the messages.
//
//        $view = new \view\LoginView();
//        $message = "";
//        $sesh = isset($_SESSION['isLoggedIn']) ?? false;
//        $keepMeLoggedIn = isset($_REQUEST["LoginView::KeepMeLoggedIn"]) ?? false;

//        if (isset($_COOKIE["LoginView::CookieName"]) && $_COOKIE["LoginView::CookiePassword"]) {
//            $this->db->getUser($_COOKIE["LoginView::CookieName"], $_COOKIE["LoginView::CookiePassword"]);
//        }
//
//        if ($_SERVER["REQUEST_METHOD"] == "POST" && !$sesh) {
//
//            if (isset($_REQUEST["register"])) {
//                // too long of a statment
//                $message = $this->db->saveUser($_REQUEST["RegisterView::UserName"], $_REQUEST["RegisterView::Password"],
//                    $_REQUEST["RegisterView::PasswordRepeat"]);
//
//
//            } else if (isset($_REQUEST["LoginView::Login"])) {
//                // can i change the query string to be login?
//
//                $postedName = $_REQUEST["LoginView::UserName"];
//                $postedPassword = $_REQUEST["LoginView::Password"];
//
//                if (empty($postedName)) {
//                    $message = "Username is missing";
//
//                } else if (empty($postedPassword)) {
//                    $message = "Password is missing";
//                    $view->setEnteredName($postedName);
//                } else {
//                    $message = $this->db->getUser($postedName, $postedPassword, $keepMeLoggedIn);
//                    $view->setEnteredName($postedName);
//                }
//
//            }
//
//        }  else {
//
//            if (isset($_REQUEST["LoginView::Logout"])) {
//                session_unset();
//                session_destroy();
//                setcookie("PHPSESSID", 0, time() - 3600);
//                $message = "Bye bye!";
//            }
//
//        }
//
//        return $message;
    }

    public function defaultView() {

        $this->setViewAction("defaultView");

    }

    public function registerView() {

        $this->setViewAction("registerView");

    }


    public function registerUser(){


    }

    public function loggedIn() {

    }

    function setDb($db) {
        $this->db = $db;
    }
}