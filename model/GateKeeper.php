<?php

namespace model;
require_once('UserDatabase.php');
include(dirname(__FILE__, 2) . "/config.php");



interface GateKeeperListener {

    public function loginFailed();

    public function loggedIn();

    public function logOut();

    public function registered();

    public function sessionedIn();

    public function defaultView();

}

class GateKeeper
{
    private $dbConnection;

    public function connectDatabase($db) {
        $this->dbConnection = $db;

    }

    private $isLoggedIn = false;
    private $loggedInUser = false;

    private function setIsLoggedIn(bool $status) {
        $this->isLoggedIn = $status;
    }

    public function getIsLoggedIn() {
        return $this->isLoggedIn;
    }

    public function getLoggedInUser() {
        return $this->loggedInUser;
    }


    public function logIn(User $user, GateKeeperListener $gateKeeperListener) {
        $this->dbConnection->verifyPasswordFor($user); //TODO rename getUser?
        $this->setIsLoggedIn(true);
        $gateKeeperListener->loggedIn();

    }

    public function registerUser(User $user, GateKeeperListener $gateKeeperListener) {
        $this->dbConnection->saveUser($user);
        $gateKeeperListener->registered();

    }

    public function logOut(GateKeeperListener $gateKeeperListener) {
        $this->setIsLoggedIn(false);
        $gateKeeperListener->logOut();

    }

    public function backWithSession(GateKeeperListener $gateKeeperListener) {
        $this->setIsLoggedIn(true);
        $gateKeeperListener->loggedIn();
    }

    public function sessionIn(User $user, GateKeeperListener $gateKeeperListener){
        $validSession = $this->dbConnection->verifySessionFor($user);

        if ($validSession) {
            $this->setIsLoggedIn(true);
            $gateKeeperListener->sessionedIn();

        } else {
            $gateKeeperListener->defaultView();
        }

    }


}