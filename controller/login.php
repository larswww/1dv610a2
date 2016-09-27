<?php
namespace Controller;
use view;

class AuthController {

    function login($username, $password) {

        $view = new \view\LoginView();
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $postedName = $_POST[$username];
            $postedPassword = $_POST[$password];

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

        return $message;
    }

    function setDb($db) {
        $this->db = $db;
    }
}