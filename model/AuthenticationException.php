<?php

class AuthenticationException extends Exception {

    public function errorMessage() {

        return $this->getMessage();
    }
}