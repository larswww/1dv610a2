<?php
/**
 * Created by PhpStorm.
 * User: MBAi
 * Date: 14/10/2016
 * Time: 1:10 PM
 */

namespace model;
require_once('Validate.php');


class User
{
    private $password;
    private $username;
    private $attemptedUsername = "";
    private $validate;
    private $keepMeLoggedIn;


    public function __construct() {
        $this->validate = new \model\Validate();
        $this->validate->setCurrentUser($this);
    }

    public function getAttemptedUsername() {
        return $this->attemptedUsername;
    }

    public function setAttemptedUsername(string $uname) {
        $this->attemptedUsername = $uname;
    }


    public function getPassword(){
        return $this->password;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getKeepLoggedIn(){
        return $this->keepMeLoggedIn;
    }


    public function setUsername($enteredUserName){
        $this->validate->sanitizeUsername($enteredUserName);
        $this->attemptedUsername = $enteredUserName;
        $this->validate->username($enteredUserName);
        $this->username = $enteredUserName;
    }

    public function setPassword($pw) {
        $this->standardValidations($pw);
        $this->validate->password($pw, $pw);

        $this->password = $pw;
    }

    public function registrationAttempt($pw, $repeatedPw, $username) {
        $this->setUsername($username);
        $this->standardValidations($pw); // TODO do i need these now?
        $this->standardValidations($repeatedPw);
        $this->validate->registrationAttempt($pw, $repeatedPw, $username);
        $this->setPassword($pw);

    }

    public function setKeepLoggedIn(bool $userChoice){

        $this->keepMeLoggedIn = $userChoice;

    }

    private function standardValidations($input){
        $this->validate->containsScript($input);
        $this->validate->sanitizeSQL($input);
    }


}