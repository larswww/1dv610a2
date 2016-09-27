<?php
namespace model;

class userDB {
    private $db;

    public function __construct($config) {
        try {
            var_dump($config);
            $dbSettingString = "mysql:host=" . $config["host"] . ";dbname=" . $config["name"] . ";port=" . $config["port"] . ";charset=" . $config["charset"];
            var_dump($dbSettingString);
            $this->db = new \PDO($dbSettingString, $config["username"], $config["password"]);

//            $this->db = new \PDO(
//                'mysql:host=127.0.0.1;dbname=csquiz;port=8889;charset=utf8',
//                'root',
//                'root'
//            );

        } catch (\PDOException $exception) {
            // Database connection failed
            echo "db connection failed";
            exit;
        }
    }

    public function saveUser($username, $password) {

        try {
            $userSchema = $this->db->prepare("INSERT INTO users (username, password)" . "VALUES (:username, :password)");

            $pwHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
            $userSchema->execute(array(
                "username" => $username,
                "password" => $pwHash
            ));

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getUser($username, $password) {

        try {
            $query = $this->db->prepare("SELECT username, password FROM users WHERE username = '$username'");
            $query->execute();
            $user = $query->fetch();
            $isPasswordCorrect = password_verify($password, $user["password"]);
            $isUsernameSame = $user["username"] === $username;

            if (!$isPasswordCorrect || !$isUsernameSame) {
                return "Wrong name or password";
            } else {
                return "Welcome";
            }

        } catch (\Exception $e) {
            return "Login Error - Please try again";

        }
    }
}
