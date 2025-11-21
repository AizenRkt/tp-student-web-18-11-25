<?php

use app\controllers\Controller;
use app\controllers\grade\GradeController;

use app\models\grade\GradeModel;
use app\helpers\JWT;
use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// --------------------
// Middleware for /students*
// --------------------
Flight::route('/students*', function() {
    // Try both methods to get Authorization header
    $headers = getallheaders();
    // Most reliable way to get the Authorization header
	if (!isset($_SERVER['HTTP_AUTHORIZATION']) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}

// Also handle cases where it's in a different format (some servers)
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    foreach (apache_request_headers() as $key => $value) {
        if (strcasecmp($key, 'Authorization') === 0) {
            $_SERVER['HTTP_AUTHORIZATION'] = $value;
            break;
        }
    }
}
$auth = $_SERVER['HTTP_AUTHORIZATION'] 
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
        ?? '';

    if (!$auth || !str_starts_with($auth, 'Bearer ')) {
        Flight::json([
            'status' => 'error',
            'data' => null,
            'error' => 'Missing or invalid Authorization header'
        ], 401);
        var_dump($auth); exit;

    }

    $token = substr($auth, 7);
	
    // Validate token
    $payload = JWT::validate($token);

    if (!$payload) {
        Flight::json([
            'status' => 'error',
            'data' => null,
            'error' => 'Token invalid or expired'
        ], 401);
        exit;
    }

    // Store logged-in user for controllers
    Flight::set('user', $payload);
});

// --------------------
// Routes
// --------------------
$Controller = new Controller();
Flight::route('GET /', [$Controller, 'acceuil']);

// Authentication
Flight::route('POST /login', ['app\controllers\AuthController', 'login']);

// Student routes (protected by middleware above)
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
