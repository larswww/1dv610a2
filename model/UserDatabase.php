<?php
namespace model;

class UserDatabase {
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
            $userTable = "CREATE TABLE IF NOT EXISTS users (username VARCHAR(30) NOT NULL,
                                                            password VARCHAR(60) NOT NULL)";
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db->exec($userTable);

        } catch (\PDOException $exception) {
            echo "db connection failed";
            exit;
        }
    }

    public function saveUser(User $user) {

        $username = $user->getUsername();
        $password = $user->getPassword();

        //TODO should save and fetch as lower case?

        $checkUsernameQuery = $this->db->prepare("SELECT username, password FROM users WHERE username = '$username'");
        $checkUsernameQuery->execute();
        $foundUsername = $checkUsernameQuery->fetch();

        if ($foundUsername) {
            throw new \Exception("User exists, pick another username.");
        }

            try {
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

    public function getUser(User $user) {
        $username = $user->getUsername();
        $password = $user->getPassword();

            $query = $this->db->prepare("SELECT username, password FROM users WHERE username = '$username'");
            $query->execute();
            $userQuery = $query->fetch();

        if ($user->getKeepLoggedIn()) {

        }

            $isPasswordCorrect = password_verify($password, $userQuery["password"]); //TODO is that really a userQuery?
            $isUsernameSame = $user->getUsername() === $userQuery["username"]; // TODO is this needed? Would the DB query actually return a username if it wasnt the same? legacy from sanitize?

            if (!$isPasswordCorrect || !$isUsernameSame) {
                throw new \Exception("Wrong name or password");
            }
    }
}
