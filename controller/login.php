<?php
namespace Controller;
use view;

class AuthController {

    function router() {

        $view = new \view\LoginView();
        $message = "";
        $sesh = isset($_SESSION['isLoggedIn']) ?? false;
        $keepMeLoggedIn = isset($_REQUEST["LoginView::KeepMeLoggedIn"]) ?? false;

//        if (isset($_COOKIE["LoginView::CookieName"]) && $_COOKIE["LoginView::CookiePassword"]) {
//            $this->db->getUser($_COOKIE["LoginView::CookieName"], $_COOKIE["LoginView::CookiePassword"]);
//        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && !$sesh) {

            if (isset($_REQUEST["register"])) {
                // too long of a statment
                $message = $this->db->saveUser($_REQUEST["RegisterView::UserName"], $_REQUEST["RegisterView::Password"],
                    $_REQUEST["RegisterView::PasswordRepeat"]);


            } else if (isset($_REQUEST["LoginView::Login"])) {
                // can i change the query string to be login?

                $postedName = $_REQUEST["LoginView::UserName"];
                $postedPassword = $_REQUEST["LoginView::Password"];

                if (empty($postedName)) {
                    $message = "Username is missing";

                } else if (empty($postedPassword)) {
                    $message = "Password is missing";
                    $view->setEnteredName($postedName);
                } else {
                    $message = $this->db->getUser($postedName, $postedPassword, $keepMeLoggedIn);
                    $view->setEnteredName($postedName);
                }

            }

        }  else {

            if (isset($_REQUEST["LoginView::Logout"])) {
                session_unset();
                session_destroy();
                setcookie("PHPSESSID", 0, time() - 3600);
                $message = "Bye bye!";
            }




        }

        return $message;
    }

    function setDb($db) {
        $this->db = $db;
    }
}