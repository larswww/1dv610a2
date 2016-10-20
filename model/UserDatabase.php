<?php
namespace model;

class UserDatabase {

    // $config is fed into constructor via index.php (which in turn takes it from a file one level above root
    public function __construct($config) {

        $dbSettingString = "mysql:host=" . $config["host"] . ";dbname=" . $config["name"] . ";port=" . $config["port"] . ";charset=" . $config["charset"];
        $this->db = new \PDO($dbSettingString, $config["username"], $config["password"]);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->createTableIfDoesntExist();

    }

    private function createTableIfDoesntExist() {
        $userTable = "CREATE TABLE IF NOT EXISTS users (username VARCHAR(30) NOT NULL,
                                                        password VARCHAR(60) NOT NULL)";
        $this->db->exec($userTable);

    }

    public function saveUser(User $user) {

        $foundUsername = $this->getUser($user);

        if ($foundUsername) {
            throw new \AuthenticationException("User exists, pick another username.");
        }

        $userSchema = $this->db->prepare("INSERT INTO users (username, password)" . "VALUES (:username, :password)");
        $pwHash = password_hash($user->getPassword(), PASSWORD_BCRYPT, ["cost" => 12]);

        $userSchema->execute(array(
            "username" => $user->getUsername(),
            "password" => $pwHash
        ));

    }

    public function verifyPasswordFor(User $user) {

        $password = $user->getPassword();
        $userQuery = $this->getUser($user);

        if ($user->getKeepLoggedIn()) {

        }

            $isPasswordCorrect = password_verify($password, $userQuery["password"]);
            $isUsernameSame = $user->getUsername() === $userQuery["username"];

            if (!$isPasswordCorrect || !$isUsernameSame) {
                throw new \AuthenticationException("Wrong name or password");
            }
    }

    private function getUser(User $user) {
        $username = $user->getUsername();

        $query = $this->db->prepare("SELECT username, password FROM users WHERE username = '$username'");
        $query->execute();
        $userQuery = $query->fetch();

        return $userQuery;

    }
}
