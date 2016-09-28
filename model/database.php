<?php
namespace model;

class userDB {
    private $db;
    private $isLoggedIn = false;

    /**
     * userDB constructor - connects to the database,
     * @param $config database config array defined in an external file above project root
     * @param $dbSettingsString builds a connection string for the PDO layer using the config array passed in via index.php
     */
    public function __construct($config) {
        try {
            $dbSettingString = "mysql:host=" . $config["host"] . ";dbname=" . $config["name"] . ";port=" . $config["port"] . ";charset=" . $config["charset"];
            $this->db = new \PDO($dbSettingString, $config["username"], $config["password"]);

        } catch (\PDOException $exception) {
            echo "db connection failed";
            exit;
        }
    }

    public function saveUser($username, $password, $passwordRepeat) {
        $message = "";
        $validUsername = true;

        if (strlen($username) < 4) {
            $message .= "Username has too few characters, at least 3 characters. ";
            $validUsername = false;
        }

        if (strlen($password) < 7) {
            $message .= "Password has too few characters, at least 6 characters.";
        }

        if ($message === "") {
            // hash password using bcrypt then save into database
            try {
                echo "tried reg";
                $userSchema = $this->db->prepare("INSERT INTO users (username, password)" . "VALUES (:username, :password)");

                $pwHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
                $userSchema->execute(array(
                    "username" => $username,
                    "password" => $pwHash
                ));

            } catch (\Exception $e) {
                $validUsername = false;
                echo $e->getMessage();
            }
        }

        if ($validUsername) {
            $regView = new \view\RegisterView();
            // change this to using isset and filter it first.
            $regView->setEnteredName($_REQUEST["RegisterView::UserName"]);
        }


        return $message;
    }

    public function getUser($username, $password) {

        // returns a message status string based on outcome of db query
        // getUser is called from controller using data in $_POST for username and password
        try {
            $query = $this->db->prepare("SELECT username, password FROM users WHERE username = '$username'");
            $query->execute();
            $user = $query->fetch();

            $isPasswordCorrect = password_verify($password, $user["password"]);
            $isUsernameSame = $user["username"] === $username;

            if (!$isPasswordCorrect || !$isUsernameSame) {
                return "Wrong name or password";
            } else {
                $this->setIsLoggedIn(true);
                return "Welcome";
            }

        } catch (\Exception $e) {
            return "Login Error - Please try again";

        }
    }

    private function setIsLoggedIn(bool $status) {
        $this->isLoggedIn = $status;
    }

    public function getIsLoggedIn() {
        return $this->isLoggedIn;
    }

}
