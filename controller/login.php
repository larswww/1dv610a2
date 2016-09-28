<?php
namespace Controller;
use view;

class AuthController {

    function router() {

        $view = new \view\LoginView();
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if ($_SERVER["QUERY_STRING"] == "register") {
                // too long of a statment
                $message = $this->db->saveUser($_REQUEST["RegisterView::UserName"], $_REQUEST["RegisterView::Password"],
                    $_REQUEST["RegisterView::PasswordRepeat"]);



            } else if ($_SERVER["QUERY_STRING"] == "") {
                // can i change the query string to be login?
                $postedName = $_REQUEST["LoginView::UserName"];
                $postedPassword = $_REQUEST["LoginView::Password"];

                if (empty($postedName)) {
                    $message = "Username is missing";

                } else if (empty($postedPassword)) {
                    $message = "Password is missing";
                    $view->setEnteredName($postedName);
                } else {
                    $message = $this->db->getUser($postedName, $postedPassword);
                    $view->setEnteredName($postedName);
                }

            }

        }

        return $message;
    }

    function setDb($db) {
        $this->db = $db;
    }
}