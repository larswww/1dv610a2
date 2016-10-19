<?php
namespace controller;
use model;
use view;

class AuthController {
    private $view;
    private $gateKeeper;

    public function setViewAndKeeper(view\LoginView $view, model\GateKeeper $keeper) {
        $this->view = $view;
        $this->gateKeeper = $keeper;
    }


//    public function __construct()
//    {
//        $this->gateKeeper = new model\GateKeeper();
//        //$this->view = new view\LoginView();
//
//
//    }



    function router() {


        if ($this->gateKeeper->getIsLoggedIn()) {

            if ($this->view->userWantsToLogout()) {

                $this->gateKeeper->logOut($this->view);
            }

        } else if ($this->view->backWithSession()) {

            if ($this->view->userWantsToLogout()) {

                $this->gateKeeper->logOut($this->view);

            } else {
                $this->gateKeeper->sessionIn($this->view);

            }

        } else {

            if ($this->view->userWantsToLogin()) {

                $user = $this->view->getUser();
                $this->gateKeeper->logIn($user, $this->view);
            } else if ($this->view->userWantsToRegister()) {
                $user = $this->view->getUser();

                // bypass gatekeeper and go straight to DB without login right?
                // no because gateKeeper implements the interface and can call the successful registration function.
                $this->gateKeeper->registerUser($user, $this->view);

                //$this->gateKeeper->reg
            } else {
               return $this->view->defaultView();
            }

        }
    }

    public function defaultView() {

        return $this->setViewAction("defaultView");

    }

    public function registerView() {

        $this->setViewAction("registerView");

    }


    public function registerUser(){
        $userModel = new model\User();
        $userModel->setUsername($this->UserName);
        $userModel->setPassword($this->Password, $this->PasswordRepeat);

        $this->db = new model\UserDatabase();
        $this->db->saveUser($userModel);
        $this->view->setMessage("Welcome");
        $this->defaultView();

    }

    public function loggedIn() {

        //TODO set welcome back with cookies here?
        $this->setViewAction("defaultView");

    }

    public function logOut() {
        session_unset();
        session_destroy();
        setcookie("PHPSESSID", 0, time() - 3600);
        $_SESSION['isLoggedIn'] = false;
        $this->setViewAction("logOut");
    }

    public function logIn(){
        $userModel = new model\User();
        //TODO how to call this without the repeat password? add = null in USer model?
        $userModel->setUsername($this->UserName);
        $userModel->setPassword($this->Password);
        $userModel->setKeepLoggedIn($this->KeepMeLoggedIn);

        $this->view->setEnteredName($this->UserName);
        $this->db = new model\UserDatabase();
        $this->db->getUser($userModel);

    }

    function setDb($db) {
        $this->db = $db;
    }
}