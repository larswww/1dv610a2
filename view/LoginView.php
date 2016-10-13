<?php
namespace view;
use Controller;
require_once('RegisterView.php');

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
    private static $enteredName = "";
    private $authStatus;
    public $message;


    public function setEnteredName($name) {
        return self::$enteredName = $name;
    }

    public function setController($controller) {
        $this->controller = $controller;
        //$this->message = $this->controller->router();

    }



    /**bre
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {

	    $viewFunctionToCall = $this->controller->getViewAction();
        $response = $this->$viewFunctionToCall();
        return $response;




	    // determine which view to be rendered based on the message? String dependency unfortunately.
        // can i set this in the POST variable instead?
        // calls the function suggested by the router?
//
//	    $sesh = isset($_SESSION['isLoggedIn']) ?? false;
//
//        if ($this->message === "Welcome" || $this->message === "Welcome and you will be remembered") {
//
//            if (isset($_SESSION["welcomed"])) {
//                $_SESSION["welcomed"] = true;
//                $this->message = "";
//            }
//
//            $response = $this->generateLogoutButtonHTML($this->message);
//            $this->message = "";
//
//        } else if ($sesh) {
//            $this->message = "";
//            $response = $this->generateLogoutButtonHTML($this->message);
//
//        } else if ($this->message === "Registered new user.") {
//            $this->setEnteredName($_REQUEST["RegisterView::UserName"]);
//            $response = $this->generateLoginFormHTML($this->message);
//
//        } else if (isset($_REQUEST["register"])) {
//            $regView = new RegisterView();
//            $response = $regView->generateRegisterFormHTML($this->message);
//
//        }  else {
//            $response = $this->generateLoginFormHTML($this->message);
//        }
//
//        return $response;
	}

	public function loggedIn(){

        if (isset($_SESSION["welcomed"])) {
            $_SESSION["welcomed"] = true;
            $message = "";
        }

        $response = $this->generateLogoutButtonHTML($message);
        $this->message = "";
        return $response;

    }

    public function defaultView() {
        $message = "";

        if (isset($_REQUEST["LoginView::Logout"])) {
            session_unset();
            session_destroy();
            setcookie("PHPSESSID", 0, time() - 3600);
            $message = "Bye bye!";
        }

        return $this->generateLoginFormHTML($message);
    }

    public function registerView() {
        $regView = new RegisterView();
        return $regView->generateRegisterFormHTML("");
    }

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	public function generateLogoutButtonHTML($message) {
		return '
			<form method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}
	
	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {
		return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . self::$enteredName . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
	
	//CREATE GET-FUNCTIONS TO FETCH REQUEST VARIABLES
	private function getRequestUserName() {
		//RETURN REQUEST VARIABLE: USERNAME
        return $this->name;
	}
}