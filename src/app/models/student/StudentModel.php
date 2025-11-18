<?php

namespace app\models\student;

use Flight;
use PDO;

class StudentModel {

    private static function getDb() {
        return Flight::db();
    }

    public static function insert($data) {
        $sql = "INSERT INTO student (lastName, firstNames, birthDate, registrationNumber, promotion)
                VALUES (:lastName, :firstNames, :birthDate, :registrationNumber, :promotion)";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([
            ':lastName' => $data['lastName'],
            ':firstNames' => $data['firstNames'],
            ':birthDate' => $data['birthDate'],
            ':registrationNumber' => $data['registrationNumber'],
            ':promotion' => $data['promotion'] ?? null
        ]);
        return self::getDb()->lastInsertId();
    }

    public static function getById($id) {
        $sql = "SELECT * FROM student WHERE idStudent = :id";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $sql = "SELECT * FROM student ORDER BY lastName, firstNames";
        $stmt = self::getDb()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $sql = "DELETE FROM student WHERE idStudent = :id";
        $stmt = self::getDb()->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
