<?php

namespace app\controllers\semester;

use app\models\semester\SemesterModel;
use Exception;
use Flight;

class SemesterController {

    public static function getAll() {
        try {
            $data = SemesterModel::getAll();
            Flight::json(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            Flight::json(["status" => "error", "error" => $e->getMessage()], 404);
        }
    }

    public static function getById($idSemester) {
        try {
            $data = SemesterModel::getById($idSemester);
            Flight::json(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            Flight::json(["status" => "error", "error" => $e->getMessage()], 404);
        }
    }

    public static function getByYear($idAcademicYear) {
        try {
            $data = SemesterModel::getByYear($idAcademicYear);
            Flight::json(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            Flight::json(["status" => "error", "error" => $e->getMessage()], 404);
        }
    }

    public static function getBySemesterYear($idSemester, $year) {
        try {
            $students = SemesterModel::getStudentsBySemesterYear($idSemester, $year);
            Flight::json([
                "status" => "success",
                "data" => $students,
                "error" => null
            ]);
        } catch (\Exception $e) {
            Flight::json([
                "status" => "error",
                "data" => null,
                "error" => $e->getMessage()
            ], 404);
        }
    }
}
