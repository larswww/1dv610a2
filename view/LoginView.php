<?php
namespace view;
use controller\AuthController;
use model\GateKeeper;
use model\GateKeeperListener;
use model\User;

require_once('RegisterView.php');
require_once('./model/User.php');
require_once('./model/GateKeeper.php');

class LoginView implements GateKeeperListener {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
    private static $enteredName = "";
    private static $registerName = 'RegisterView::UserName';
    private static $registerPassword = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
    private static $doRegistration = 'RegisterView::DoRegistration';

    private $message = "";
    private $gateKeeper;
    private $wantsToLogin;
    private $wantsToLogout;
    private $wantsToRegister;
    private $user;
    private $authController;
    private $shouldDisplayError;
    private $response;


    public function setEnteredName($name) {
        return self::$enteredName = $name;
    }

    public function setGateKeeper(GateKeeper $keeper) {
        $this->gateKeeper = $keeper;
    }

    public function getGateKeeper() {
        return $this->gateKeeper;
    }

    public function setController(AuthController $controller) {
        $this->authController = $controller;
    }

    public function setMessage($message) {
        $this->message .= $message;

    }

    // TODO: PHP auto implemented prooperties?
    public function userWantsToLogout() {
        return $this->wantsToLogout;
    }

    private function setWantsToLogout(bool $want) {
        $this->wantsToLogout = $want;
    }

    public function userWantsToLogin() {
        return $this->wantsToLogin;
    }

    private function setWantsToLogin(bool $want) {
        $this->wantsToLogin = $want;

    }

    public function userWantsToRegister() {
        return $this->wantsToRegister;

    }

    private function setWantsToRegister(bool $want) {
        $this->wantsToRegister = $want;

    }

    public function getUser() {
        return $this->user;

    }

    private function setUser(\model\User $user) {
        $this->user = $user;
    }

    public function handleError($msg) {
        // set the message
        // continue executing response with whatever view was wanted
        $this->setMessage($msg);
        $this->shouldDisplayError = true;

    }

    public function shouldDisplayError() {
        return $this->shouldDisplayError;
    }

    private function setResponse(string $res) {
        $this->response = $res;

    }

    public function getResponse() {
        return $this->response;
    }

    public function getUserInput() {

        if ($this->gateKeeper->getIsLoggedIn()) {

            if (isset($_REQUEST[self::$logout])) {
                $this->setWantsToLogout(true);

                session_unset();
                session_destroy();
                setcookie("PHPSESSID", 0, time() - 3600);
                $message = "Bye bye!";
            }

        } else {



            // wants to submit registration
            if (isset($_REQUEST[self::$login])) {

                // change to:
                $this->setWantsToLogin(true);

                //TODO abstract into a createSetUser() method?
                $postedName = $_REQUEST[self::$name];

                //TODO I'm now double checking this somewhere.
                if (empty($postedName)) {
                    throw new \Exception("Username is missing");
                }

                $this->setEnteredName($postedName);

                $postedPassword = $_REQUEST[self::$password];
                $user = new \model\User();
                $user->setUsername($postedName);
                $user->setPassword($postedPassword);

                $this->setUser($user);

            } else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_REQUEST["register"])) {

                $this->setWantsToRegister(true);

                $user = new User();
                // TODO there must be a better way to import these values?
                $this->setUser($user);
                $user->registrationAttempt($_REQUEST[self::$registerPassword], $_REQUEST[self::$passwordRepeat], $_REQUEST[self::$registerName]);



            } else if (isset($_REQUEST["register"]) && $_REQUEST["register"] === "1") {
                $this->setWantsToRegister(true);
                $this->shouldDisplayError = true; //TODO fulhaxx test, fixa. bypassController?

            }

        }
    }


    /**bre
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {

        // inte inloggad

        // TODO put this in one logged in / not logged in if else

        if ($this->shouldDisplayError()) {

            if ($this->userWantsToRegister()) {
                return $this->registerView();
            }

            if ($this->userWantsToLogin()) {
                return $this->defaultView();
            }

            if ($this->userWantsToLogout()) {
                // any error for logout?
            }
            // skip all of this and just show show show it yah

        }

        $this->authController->router();

	}

	public function loggedIn(){
	    $message = "Welcome";

        if (isset($_SESSION["welcomed"])) {
            $_SESSION["welcomed"] = true;
            $message = "";
        }

        $response = $this->generateLogoutButtonHTML($message);
        $this->setResponse($response);

    }

    public function loginFailed()
    {
        // TODO: Implement loginFailed() method.
        $this->setMessage("Login failed");
    }

    public function registered()
    {
        // TODO: Implement registered() method.
        $_SERVER['QUERY_STRING'] = "/test.php";
        $_SERVER['REQUEST_URI'] = "a2/test.php";
        unset($_REQUEST["register"]);
        $this->setMessage("Registered new user.");
        $this->defaultView();
    }

    public function logOut(){
        $this->message = "Bye bye!";
        $this->defaultView();
    }

    public function defaultView() {

        $response = $this->generateLoginFormHTML($this->message);
        $this->setResponse($response);
    }

    public function registerView() {
        $regView = new RegisterView();
        $user = $this->getUser();
        $attemptedName = isset($user) ? $user->getAttemptedUsername() : "";
        $regView->setEnteredName($attemptedName);
        $response = $regView->generateRegisterFormHTML($this->message);
        $this->setResponse($response);

    }

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	public function generateLogoutButtonHTML($message) {
		return '
			<form method="post" action="?">
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
			<form method="post" action="?"> 
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