<?php

namespace app\models\grade;

use Flight;
use PDO;

class GradeModel {

    private static function getDb() {
        return Flight::db();
    }

    public static function insert($data) {
        $sql = "INSERT INTO studentGrade (idStudent, idSubject, idSemester, idExamSession, grade, gradeDate)
                VALUES (:idStudent, :idSubject, :idSemester, :idExamSession, :grade, :gradeDate)";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([
            ':idStudent' => $data['idStudent'],
            ':idSubject' => $data['idSubject'],
            ':idSemester' => $data['idSemester'],
            ':idExamSession' => $data['idExamSession'],
            ':grade' => $data['grade'],
            ':gradeDate' => $data['gradeDate']
        ]);
        return self::getDb()->lastInsertId();
    }

    public static function getById($id) {
        $sql = "SELECT * FROM studentGrade WHERE idGrade = :id";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $sql = "SELECT * FROM studentGrade ORDER BY gradeDate DESC";
        $stmt = self::getDb()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $sql = "DELETE FROM studentGrade WHERE idGrade = :id";
        $stmt = self::getDb()->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public static function getByStudent($idStudent) {
        $sql = "SELECT g.*, s.lastName, s.firstNames, d.idSubject, d.idSemester
                FROM studentGrade g
                JOIN student s ON g.idStudent = s.idStudent
                JOIN subjectDetailSemesterYear d ON g.idSubject = d.idSubject
                WHERE g.idStudent = :idStudent
                ORDER BY g.gradeDate DESC";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':idStudent' => $idStudent]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByStudentReg($registrationNumber) {
        $sql = "SELECT g.*, s.lastName, s.firstNames, d.idSubject, d.idSemester
                FROM studentGrade g
                JOIN student s ON g.idStudent = s.idStudent
                JOIN subjectDetailSemesterYear d ON g.idSubject = d.idSubject
                WHERE s.registrationNumber = :registrationNumber
                ORDER BY g.gradeDate DESC";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':registrationNumber' => $registrationNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByStudentSemester($idStudent, $semesterName) {
        $sql = "SELECT g.idGrade, g.idStudent, g.idSubject, g.idSemester, g.idExamSession, g.grade, g.gradeDate,
                    s.lastName, s.firstNames,
                    sub.subjectCode, sub.name AS subjectName,
                    sem.semesterName
                FROM studentGrade g
                JOIN student s ON g.idStudent = s.idStudent
                JOIN subject sub ON g.idSubject = sub.idSubject
                JOIN semester sem ON g.idSemester = sem.idSemester
                WHERE g.idStudent = :idStudent
                AND sem.semesterName = :semesterName
                ORDER BY g.gradeDate DESC";

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([
            ':idStudent' => $idStudent,
            ':semesterName' => $semesterName
        ]);

        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($grades)) { // <-- ici
            throw new \Exception("Grade not found");
        }

        return $grades;
    }

    public static function getByStudentYear($idStudent, $yearName) {
        $sql = "SELECT g.idGrade, g.idStudent, g.idSubject, g.idSemester, g.idExamSession, g.grade, g.gradeDate,
                       s.lastName, s.firstNames,
                       sub.subjectCode, sub.name AS subjectName,
                       sem.semesterName, ay.name AS yearName
                FROM studentGrade g
                JOIN student s ON g.idStudent = s.idStudent
                JOIN subject sub ON g.idSubject = sub.idSubject
                JOIN semester sem ON g.idSemester = sem.idSemester
                JOIN academicYear ay ON sem.idAcademicYear = ay.idAcademicYear
                WHERE g.idStudent = :idStudent
                  AND ay.name = :yearName
                ORDER BY sem.semesterName, g.gradeDate DESC";

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([
            ':idStudent' => $idStudent,
            ':yearName' => $yearName
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            throw new \Exception("Grades not found for this year");
        }

        $data = [];
        foreach ($results as $row) {
            $sem = $row['semesterName'];
            if (!isset($data[$sem])) {
                $data[$sem] = [];
            }
            $data[$sem][] = $row;
        }

        return $data;
    }
}