<?php

//importation de controller
use app\controllers\Controller;
use app\controllers\grade\GradeController;

use app\models\grade\GradeModel;

//importation lié flight
use flight\Engine;
use flight\net\Router;

//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$Controller = new Controller();
$router->get('/', [ $Controller, 'acceuil' ]);

Flight::route('GET /students', ['app\controllers\student\StudentController', 'getAll']);
Flight::route('GET /students/@id', ['app\controllers\student\StudentController', 'getById']);
Flight::route('POST /students', ['app\controllers\student\StudentController', 'create']);
Flight::route('DELETE /students/@id', ['app\controllers\student\StudentController', 'delete']);


Flight::route('GET /grades', [GradeController::class, 'getAll']);
Flight::route('GET /grades/@id', [GradeController::class, 'getById']);
Flight::route('GET /grades/student/@idStudent', [GradeController::class, 'getByStudent']);
Flight::route('GET /grades/studentReg/@registrationNumber', [GradeController::class, 'getByStudentReg']);
Flight::route('POST /grades', [GradeController::class, 'create']);
Flight::route('DELETE /grades/@id', [GradeController::class, 'delete']);

// Notes par semestre
Flight::route('GET /grades/student/@idStudent/semester/@semesterName', function($idStudent, $semesterName) {
    GradeController::getByStudentSemester($idStudent, $semesterName);
});

// Notes par année
Flight::route('GET /grades/student/@idStudent/year/@yearName', function($idStudent, $yearName) {
    GradeController::getByStudentYear($idStudent, $yearName);
});


?>