<?php

namespace app\controllers\grade;

use app\models\grade\GradeModel;
use Exception;
use Flight;

class GradeController {

    public static function getAll() {
        try {
            $grades = GradeModel::getAll();
            Flight::json([
                'status' => 'success',
                'data' => $grades,
                'error' => null
            ], 200);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function getById($id) {
        try {
            $grade = GradeModel::getById($id);
            if (!$grade) {
                Flight::json([
                    'status' => 'error',
                    'data' => null,
                    'error' => 'Grade not found'
                ], 404);
                return;
            }
            Flight::json([
                'status' => 'success',
                'data' => $grade,
                'error' => null
            ], 200);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data->getData();
            $id = GradeModel::insert($data);
            $grade = GradeModel::getById($id);
            Flight::json([
                'status' => 'success',
                'data' => $grade,
                'error' => null
            ], 201);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function delete($id) {
        try {
            $deleted = GradeModel::delete($id);
            if (!$deleted) {
                Flight::json([
                    'status' => 'error',
                    'data' => null,
                    'error' => 'Grade not found or could not be deleted'
                ], 404);
                return;
            }
            Flight::json([
                'status' => 'success',
                'data' => ['deletedId' => $id],
                'error' => null
            ], 200);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function getByStudent($idStudent) {
        try {
            $grades = GradeModel::getByStudent($idStudent);
            Flight::json([
                'status' => 'success',
                'data' => $grades,
                'error' => null
            ], 200);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function getByStudentReg($registrationNumber) {
        try {
            $grades = GradeModel::getByStudentReg($registrationNumber);
            Flight::json([
                'status' => 'success',
                'data' => $grades,
                'error' => null
            ], 200);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function getByStudentSemester($idStudent, $semesterName) {
        try {
            $data = GradeModel::getByStudentSemester($idStudent, $semesterName);
            Flight::json(["status" => "success", "data" => $data, "error" => null]);
        } catch (Exception $e) {
            Flight::json(["status" => "error", "data" => null, "error" => $e->getMessage()], 404);
        }
    }

    public static function getByStudentYear($idStudent, $yearName) {
        try {
            $data = GradeModel::getByStudentYear($idStudent, $yearName);
            Flight::json(["status" => "success", "data" => $data, "error" => null]);
        } catch (Exception $e) {
            Flight::json(["status" => "error", "data" => null, "error" => $e->getMessage()], 404);
        }
    }

    public function getReleve($idStudent, $idSemester) {
        try {
            $releve = GradeModel::getReleveNote($idStudent, $idSemester);

            $total = 0;
            $totalCredits = 0;
            foreach ($releve as $item) {
                $total += $item['grade'] * $item['credits'];
                $totalCredits += $item['credits'];
            }
            $moyenne = $totalCredits > 0 ? round($total / $totalCredits, 2) : 0;

            $response = [
                'status' => 'success',
                'data' => [
                    'releve' => $releve,
                    'moyenne' => $moyenne
                ]
            ];

            Flight::json($response);

        } catch (\Exception $e) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => $e->getMessage()
            ], 400);
        }
    }

}
