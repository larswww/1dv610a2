<?php
namespace Controller;
use view;

class AuthController {

    function login($username, $password) {

        $view = new \view\LoginView();
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $postedName = $_POST[$username];

            if (empty($_POST[$username])) {
                $message = "Username is missing";

            } else if (empty($_POST[$password])) {
                $message = "Password is missing";
                $view->setEnteredName($_POST[$username]);
            } else {
                $message = $this->db->getUser($_POST[$username], $_POST[$password]);
                $view->setEnteredName($_POST[$username]);
            }
        }

        return $message;
    }

    function setDb($db) {
        $this->db = $db;
    }


}
/**
 * Created by PhpStorm.
 * User: MBAi
 * Date: 26/09/2016
 * Time: 5:48 PM
 */