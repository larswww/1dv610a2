<?php
namespace view;

class RegisterView {
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $messageId = 'RegisterView::Message';
    private static $registration = 'RegisterView::DoRegistration';
    private static $enteredName = "";

    public function setEnteredName($name) {
        return self::$enteredName = $name;
    }

    public function generateRegisterFormHTML($message) {
        return '      
			<h2>Register new user</h2>
			<form action="?register" method="post" enctype="multipart/form-data" value="1">
				<fieldset>
				<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					<label for="' . self::$name . '">Username :</label>
					<input type="text" size="20" name="' . self::$name .'" id="' . self::$name .'" value="' . self::$enteredName . '" />
					<br/>
					<label for="' . self::$password .'" >Password  :</label>
					<input type="password" size="20" name="' . self::$password . '" id="' . self::$password .'" value="" />
					<br/>
					<label for="' . self::$passwordRepeat .'" >Repeat password  :</label>
					<input type="password" size="20" name="' . self::$passwordRepeat .'" id="' . self::$passwordRepeat . '" value="" />
					<br/>
					<input id="submit" type="submit" name="' . self::$registration .'"  value="Register" />
					<br/>
				</fieldset>
			</form>
			 ';
    }
}