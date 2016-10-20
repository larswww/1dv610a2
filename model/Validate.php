<?php

namespace model;

class Validate
{
    private $currentUser;

    public function setCurrentUser(User $user){
        $this->currentUser = $user;
    }


    public function sanitizeSQL($input) {

        $sanitizedInput = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_HIGH);

        if ($input !== $sanitizedInput) {
            throw new \AuthenticationException("Input contains invalid characters.");
        }
    }

    public function containsScript($userInput) {

        $sanitizedInput = htmlentities($userInput, ENT_QUOTES | ENT_IGNORE, "UTF-8");

        if ($sanitizedInput !== $userInput) {
            throw new \AuthenticationException("Input contains invalid characters.");
        }

    }

    public function sanitizeUsername($username) {
        $sqlFiltered =  filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_HIGH);
        $sanitizedInput = htmlentities($username, ENT_QUOTES | ENT_IGNORE, "UTF-8");

        if ($sqlFiltered !== $username) {
            $this->currentUser->setAttemptedUsername($sqlFiltered);
            throw new \AuthenticationException("Username contains invalid characters.");
        }

        if ($sanitizedInput !== $username) {
            $this->currentUser->setAttemptedUsername($sanitizedInput);
            throw new \AuthenticationException("Username contains invalid characters.");
        }
    }

    public function username($enteredUsername){
        $minUsernameLength = 3;
        $maxUsernameLength = 30;

        $this->checkLength($enteredUsername, $minUsernameLength, $maxUsernameLength, "Username" );

    }

    public function ifUsernameIsEmpty($enteredUsername) {

        if (empty($enteredUsername)) {
            throw new \AuthenticationException("Username is missing");
        }

    }

    public function registrationAttempt($password, $repeatedPassword, $username) {
        $minUsernameLength = 3;
        $maxUsernameLength = 30;

        $minPasswordLength = 6;
        $maxPasswordLength = 30;

        $message = "";

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
            throw new \AuthenticationException($message);
        }

        if ($password !== $repeatedPassword) {

            throw new \AuthenticationException("Passwords do not match.");
        }

    }


    public function password($password, $repeatedPassword) {
        $minPasswordLength = 3;
        $maxPasswordLength = 30;

        if (empty($password)) {
            throw new \AuthenticationException("Password is missing");
        }

        $this->checkLength($password, $minPasswordLength, $maxPasswordLength, "Password");

        if ($password !== $repeatedPassword) {

            throw new \AuthenticationException("Passwords do not match.");
        }

    }

    private function checkLength($userInput, $minLength, $maxLength, $ofWhat) {
        if (strlen($userInput) > $maxLength) {

            throw new \AuthenticationException("$ofWhat has too many characters, at most " . $maxLength . " characters.");
        }

        if (strlen($userInput) < $minLength) {

            throw new \AuthenticationException("$ofWhat has too few characters, at least " . $minLength . " characters.");
        }
    }

}