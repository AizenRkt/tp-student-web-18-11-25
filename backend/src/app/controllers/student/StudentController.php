<?php

namespace app\controllers\student;

use app\models\student\StudentModel;
use Exception;
use Flight;
use app\helpers\JWT;

class StudentController {

    public static function getAll() {
        try {
            $students = StudentModel::getAll();
            Flight::json([
                'status' => 'success',
                'data' => $students,
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
            $student = StudentModel::getById($id);
            if (!$student) {
                Flight::json([
                    'status' => 'error',
                    'data' => null,
                    'error' => 'Student not found'
                ], 404);
                return;
            }
            Flight::json([
                'status' => 'success',
                'data' => $student,
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
            $id = StudentModel::insert($data);
            $student = StudentModel::getById($id);
            Flight::json([
                'status' => 'success',
                'data' => $student,
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
            $deleted = StudentModel::delete($id);
            if (!$deleted) {
                Flight::json([
                    'status' => 'error',
                    'data' => null,
                    'error' => 'Student not found or could not be deleted'
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
}
