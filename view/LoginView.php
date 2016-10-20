<?php
namespace view;
use controller\AuthController;
use model\GateKeeper;
use model\GateKeeperListener;
use model\User;

require_once('RegisterView.php');
require_once('./model/User.php');
require_once('./model/GateKeeper.php');
require_once('./model/AuthenticationException.php');

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
    private $backWithSession;
    private $user;
    private $authController;
    private $shouldBypassController;
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

    public function backWithSession() {
        return $this->backWithSession;
    }

    private function setBackWithSession(bool $want) {
        $this->backWithSession = $want;
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
        $this->setMessage($msg);
        $this->shouldBypassController = true;
    }

    public function shouldBypassController() {
        return $this->shouldBypassController;
    }

    private function setResponse(string $res) {
        $this->response = $res;
    }

    public function getResponse() {
        return $this->response;
    }


    // determine user input and initialize right action with that input
    public function getUserInput() {

        $backWithSession = isset($_SESSION['isLoggedIn']);
        $this->setBackWithSession($backWithSession);
        $userIsLoggedIn = $this->gateKeeper->getIsLoggedIn();
        $wantsToLogout = isset($_REQUEST[self::$logout]);
        $hasUserCookieSet = isset($_COOKIE[self::$cookieName]);
        $wantsToLogin = isset($_REQUEST[self::$login]);
        $postedRegistrationForm = $_SERVER["REQUEST_METHOD"] === "POST" && isset($_REQUEST["register"]);
        $clickedRegistrationLink = isset($_REQUEST["register"]) && $_REQUEST["register"] === "1";

        if ($userIsLoggedIn || $backWithSession) {

            if ($wantsToLogout) {
                $this->setWantsToLogout(true);
            }

            if ($backWithSession && $hasUserCookieSet) {
                //TODO finish or remove
//                $username = $_COOKIE[self::$cookieName];
//                $hasedCookiePassword = $_COOKIE[self::$cookiePassword];
            }

        }  else {

            if ($wantsToLogin) {
                $this->setWantsToLogin(true);
                $this->initializeLogin();

            } else if ($postedRegistrationForm) {
                $this->setWantsToRegister(true);
                $this->initializeRegistration();

            } else if ($clickedRegistrationLink) {
                $this->setWantsToRegister(true);
                $this->shouldBypassController = true;

            }
        }
    }


    // respond based on user input, bypass authentication controller if thrown error at user entry stage, or if clicking register link.
    public function response() {

        if ($this->shouldBypassController()) {

            if ($this->userWantsToRegister()) {
                return $this->registerView();
            }

            if ($this->userWantsToLogin()) {
                return $this->defaultView();
            }
        }

        $this->authController->router();
    }



    public function initializeUser(string $name) {
        $this->setEnteredName($name);
        $this->user = new User();

    }

    public function initializeLogin() {

        $postedName = $_REQUEST[self::$name];
        $postedPassword = $_REQUEST[self::$password];

        if (empty($postedName)) {
            throw new \AuthenticationException("Username is missing");
        }

        $this->initializeUser($postedName);

        $this->user->setUsername($postedName);
        $this->user->setPassword($postedPassword);
        $this->user->setKeepLoggedIn(isset($_REQUEST[self::$keep]));

    }

    public function initializeRegistration() {

        $postedName = $_REQUEST[self::$registerName];
        $this->initializeUser($postedName);
        $this->user->registrationAttempt($_REQUEST[self::$registerPassword], $_REQUEST[self::$passwordRepeat], $_REQUEST[self::$registerName]);
    }


	// these actions are called at the end by the GateKeeper interface

    public function defaultView() {
        $response = $this->generateLoginFormHTML($this->message);
        $this->setResponse($response);
    }

	public function loggedIn(){
	    $message = "Welcome";
        $_SESSION['isLoggedIn'] = $this->gateKeeper->getIsLoggedIn();

        if($this->user->getKeepLoggedIn()) {
            $this->setCookieSession();
            $message .= " and you will be rememebered";
        }

        if (isset($_SESSION["welcomed"])) {
            $_SESSION["welcomed"] = true;
            $message = "";
        }

        $this->setMessage($message);
        $response = $this->generateLogoutButtonHTML($this->message);
        $this->setResponse($response);
    }

    public function logOut(){
        $this->setMessage("Bye bye!");
        $_SESSION['isLoggedIn'] = false;
        $_SESSION['welcomed'] = false;
        $this->unsetCookieSession();
        $this->defaultView();
    }

    private function setCookieSession() {
        $username = $this->user->getUsername();
        $password = $this->user->getPassword();

        $userSessionVariables = $_SERVER["HTTP_USER_AGENT"] . $_SERVER["HTTP_ACCEPT_LANGUAGE"];
        $cookiePass = md5($username . $userSessionVariables, $password);

        setcookie(self::$cookieName, $username);
        setcookie(self::$cookiePassword, $cookiePass);
    }

    private function unsetCookieSession() {
        session_unset();
        session_destroy();
        setcookie("PHPSESSID", 0, time() - 3600);
        setcookie(self::$cookieName, "", time() - 3600);
        setcookie(self::$cookiePassword, "", time() - 3600);
    }

    public function sessionedIn() {
        $response = $this->generateLogoutButtonHTML($this->message);
        $this->setResponse($response);
    }

    public function loginFailed()
    {
        $this->setMessage("Login failed");
    }

    public function registered()
    {
        $_SERVER['QUERY_STRING'] = "/test.php";
        $_SERVER['REQUEST_URI'] = "a2/test.php";
        unset($_REQUEST["register"]);
        $this->setMessage("Registered new user.");
        $this->defaultView();
    }

    public function registerView() {
        $regView = new RegisterView();
        $attemptedUsernameOrEmpty = (isset($this->user)) ? $this->user->getAttemptedUsername() : self::$enteredName;
        $regView->setEnteredName($attemptedUsernameOrEmpty);

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
			<form method="post" action="test.php">
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
			<form method="post" action="test.php"> 
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
}