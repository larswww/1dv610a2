<?php
/**
 * Created by PhpStorm.
 * User: MBAi
 * Date: 14/10/2016
 * Time: 5:11 PM
 */

namespace model;
require_once('UserDatabase.php');
include(dirname(__FILE__, 2) . "/config.php");



interface GateKeeperListener {

    public function loginFailed();

    public function loggedIn();

    public function registered();

}

class GateKeeper
{
    private $dbConnection;

    public function connectDatabase($db) {
        $this->dbConnection = $db;

    }

    // TODO should this be initialized as false?
    private $isLoggedIn = false;
    private $loggedInUser = false;

    private function setIsLoggedIn(bool $status) {
        $this->isLoggedIn = $status;
    }

    public function getIsLoggedIn() {
        return $this->isLoggedIn;
    }

    //TODO do i need logged in username?
    private function setLoggedInUser($username) {

    }

    public function getLoggedInUser() {
        return $this->loggedInUser;
    }


    public function logIn(User $user, GateKeeperListener $gateKeeperListener) {
        $this->dbConnection->getUser($user); //TODO rename getUser?
        $this->setIsLoggedIn(true);
       //TODO was this even a requirement? $this->setLoggedInUser($user->getUsername());
        $gateKeeperListener->loggedIn();

    }

    public function registerUser(User $user, GateKeeperListener $gateKeeperListener) {
        $this->dbConnection->saveUser($user);
        $gateKeeperListener->registered();

    }

    public function logOut(GateKeeperListener $gateKeeperListener) {

    }


}