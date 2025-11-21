<?php
namespace app\models;

use PDO;
use Flight;

class UserModel {

    public static function findByUsername($username) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
