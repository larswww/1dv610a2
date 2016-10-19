<?php
/**
 * Created by PhpStorm.
 * User: MBAi
 * Date: 14/10/2016
 * Time: 1:16 PM
 */

namespace model;


class Validate
{
    private $currentUser;

    public function setCurrentUser(User $user){
        $this->currentUser = $user;
    }


    public function sanitizeSQL($input) {

        //TODO error message shows username contains invalid characters, even if password contained the invalid chars.
        $sanitizedInput = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_HIGH);

        if ($input !== $sanitizedInput) {
            throw new \Exception("Input contains invalid characters.");
        }
    }

    public function containsScript($userInput) {

        $sanitizedInput = htmlentities($userInput, ENT_QUOTES | ENT_IGNORE, "UTF-8");

        if ($sanitizedInput !== $userInput) {
            throw new \Exception("Input contains invalid characters.");
        }

    }

    public function sanitizeUsername($username) {
        $sqlFiltered =  filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_HIGH);
        $sanitizedInput = htmlentities($username, ENT_QUOTES | ENT_IGNORE, "UTF-8");

        if ($sqlFiltered !== $username) {
            $this->currentUser->setAttemptedUsername($sqlFiltered);
            throw new \Exception("Username contains invalid characters.");
        }

        if ($sanitizedInput !== $username) {
            $this->currentUser->setAttemptedUsername($sanitizedInput);
            throw new \Exception("Username contains invalid characters.");
        }
    }

    public function username($enteredUsername){
        $minUsernameLength = 3;
        $maxUsernameLength = 30;

        $this->checkLength($enteredUsername, $minUsernameLength, $maxUsernameLength, "Username" );

    }

    public function ifUsernameIsEmpty($enteredUsername) {

        if (empty($enteredUsername)) {
            throw new \Exception("Username is missing");
        }

    }

    public function registrationAttempt($password, $repeatedPassword, $username) {
        $minUsernameLength = 3;
        $maxUsernameLength = 30;

        $minPasswordLength = 6;
        $maxPasswordLength = 30;

        $message = "";

        //TODO write a custom chained-exception handler or something?
        try {

            $this->checkLength($password, $minPasswordLength, $maxPasswordLength, "Password");


        } catch (\Exception $e) {
            $message .= $e->getMessage() . " ";

        }

        try {

            $this->checkLength($username, $minUsernameLength, $maxUsernameLength, "Username");

        } catch (\Exception $e) {
           $message .= $e->getMessage();
        }

        if (strlen($message) > 0) {
            throw new \Exception($message);
        }


        if ($password !== $repeatedPassword) {

            throw new \Exception("Passwords do not match.");
        }

    }


    public function password($password, $repeatedPassword) {
        $minPasswordLength = 3;
        $maxPasswordLength = 30;

        if (empty($password)) {
            throw new \Exception("Password is missing");
        }

        //TODO the length of username/password error handling string must both be sent to view in succession.
        $this->checkLength($password, $minPasswordLength, $maxPasswordLength, "Password");

        if ($password !== $repeatedPassword) {

            throw new \Exception("Passwords do not match.");
        }

    }

    private function checkLength($userInput, $minLength, $maxLength, $ofWhat) {
        if (strlen($userInput) > $maxLength) {

            throw new \Exception("$ofWhat has too many characters, at most " . $maxLength . " characters.");
        }

        if (strlen($userInput) < $minLength) {

            throw new \Exception("$ofWhat has too few characters, at least " . $minLength . " characters.");
        }
    }

}