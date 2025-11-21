<?php

namespace app\models\semester;

use Flight;
use PDO;
use Exception;

class SemesterModel  {

    private static function getDb() {
        return Flight::db();
    }

    public static function getAll() {
        $sql = "SELECT sem.idSemester, sem.semesterName, sem.idAcademicYear, ay.name AS yearName
                FROM semester sem
                JOIN academicYear ay ON sem.idAcademicYear = ay.idAcademicYear
                ORDER BY sem.idAcademicYear, sem.idSemester";

        $stmt = self::getDb()->query($sql);
        $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$semesters) {
            throw new Exception("No semesters found");
        }

        return $semesters;
    }

    public static function getById($idSemester) {
        $sql = "SELECT sem.idSemester, sem.semesterName, sem.idAcademicYear, ay.name AS yearName
                FROM semester sem
                JOIN academicYear ay ON sem.idAcademicYear = ay.idAcademicYear
                WHERE sem.idSemester = :idSemester";

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':idSemester' => $idSemester]);
        $semester = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$semester) {
            throw new Exception("Semester not found");
        }

        return $semester;
    }

    public static function getByYear($idAcademicYear) {
        $sql = "SELECT sem.idSemester, sem.semesterName
                FROM semester sem
                WHERE sem.idAcademicYear = :idAcademicYear
                ORDER BY sem.idSemester";

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':idAcademicYear' => $idAcademicYear]);
        $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$semesters) {
            throw new Exception("No semesters found for this academic year");
        }

        return $semesters;
    }

    public static function getStudentsBySemesterYear($idSemester, $year) {
        $sql = "
            SELECT 
                st.idStudent, 
                st.lastName, 
                st.firstNames, 
                st.registrationNumber, 
                st.promotion,
                COALESCE((
                    SELECT SUM(COALESCE(sg.grade, 0) * ss.credits) / SUM(ss.credits)
                    FROM semesterSubjects ss
                    LEFT JOIN studentGrade sg 
                        ON sg.idSubject = ss.idSubject
                        AND sg.idSemester = ss.idSemester
                        AND sg.idStudent = st.idStudent
                    WHERE ss.idSemester = :idSemester
                ), 0) AS moyenne
            FROM studentDetail sd
            JOIN student st ON sd.idStudent = st.idStudent
            WHERE sd.idSemester = :idSemester
            AND YEAR(sd.detailDate) = :year
            ORDER BY st.lastName, st.firstNames
        ";

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([
            ':idSemester' => $idSemester,
            ':year' => $year
        ]);

        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$students) {
            throw new \Exception("No students found for this semester and year");
        }

        return $students;
    }

}
