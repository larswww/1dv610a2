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

    // passes the user object created at view onto the GateKeeper depending on what action user watns.
    function router() {

        if ($this->gateKeeper->getIsLoggedIn()) {

            $this->check;

            if ($this->view->userWantsToLogout()) {

                $this->gateKeeper->logOut($this->view);
            }

        } else if ($this->view->backWithSession()) {

            if ($this->view->userWantsToLogout()) {

                $this->gateKeeper->logOut($this->view);

            } else if ($this->view->userWantsToLogin()){

                $user = $this->view->getUser();
                $this->gateKeeper->sessionIn($user, $this->view);

            }

        } else {

            if ($this->view->userWantsToLogin()) {

                $user = $this->view->getUser();
                $this->gateKeeper->logIn($user, $this->view);

            } else if ($this->view->userWantsToRegister()) {

                $user = $this->view->getUser();
                $this->gateKeeper->registerUser($user, $this->view);

            } else {

               return $this->view->defaultView();

            }
        }
    }

}

