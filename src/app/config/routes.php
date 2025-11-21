<?php
use app\controllers\Controller;
use app\controllers\grade\GradeController;
use app\helpers\JWT;
use Flight;

// --------------------
// JWT PROTECTION MIDDLEWARE
// --------------------
Flight::before('route', function(&$route, &$args) {
    $path = Flight::request()->url;

    // Only /login is public
    if ($path === '/login') return;

    // Extract Authorization header
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (!$auth || !str_starts_with($auth, 'Bearer ')) {
        Flight::json([
            'status' => 'error',
            'data' => null,
            'error' => 'Missing or invalid Authorization header'
        ], 401);
        Flight::stop(); // stop route execution
    }

    $token = substr($auth, 7);

    try {
        $data = JWT::decode($token);
        Flight::set('user', $data); // make user available in controllers
    } catch (Exception $e) {
        Flight::json([
            'status' => 'error',
            'data' => null,
            'error' => 'Invalid token: ' . $e->getMessage()
        ], 401);
        Flight::stop(); // stop route execution
    }
});

// --------------------
// Routes
// --------------------
$Controller = new Controller();
Flight::route('GET /', [$Controller, 'acceuil']);
Flight::route('POST /login', ['app\controllers\AuthController', 'login']);

// Student routes
Flight::route('GET /students', ['app\controllers\student\StudentController', 'getAll']);
Flight::route('GET /students/@id', ['app\controllers\student\StudentController', 'getById']);
Flight::route('POST /students', ['app\controllers\student\StudentController', 'create']);
Flight::route('DELETE /students/@id', ['app\controllers\student\StudentController', 'delete']);

// Grade routes
Flight::route('GET /grades', [GradeController::class, 'getAll']);
Flight::route('GET /grades/@id', [GradeController::class, 'getById']);
Flight::route('GET /grades/student/@idStudent', [GradeController::class, 'getByStudent']);
Flight::route('GET /grades/studentReg/@registrationNumber', [GradeController::class, 'getByStudentReg']);
Flight::route('POST /grades', [GradeController::class, 'create']);
Flight::route('DELETE /grades/@id', [GradeController::class, 'delete']);

// Notes per semester
Flight::route('GET /grades/student/@idStudent/semester/@semesterName', function($idStudent, $semesterName) {
    GradeController::getByStudentSemester($idStudent, $semesterName);
});

// Notes per year
Flight::route('GET /grades/student/@idStudent/year/@yearName', function($idStudent, $yearName) {
    GradeController::getByStudentYear($idStudent, $yearName);
});

// Debug JWT
Flight::route('GET /debug-jwt', function() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';
    Flight::json([
        'header' => $auth,
        'starts_with_bearer' => str_starts_with($auth, 'Bearer ')
    ]);
});
