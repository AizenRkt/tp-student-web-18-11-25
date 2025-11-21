<?php
use app\controllers\Controller;
use app\controllers\grade\GradeController;
use app\controllers\semester\SemesterController;

use app\models\grade\GradeModel;
use app\helpers\JWT;
use Flight;

header("Access-Control-Allow-Origin: *"); // Autoriser toutes les origines (dev)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --------------------
// JWT PROTECTION MIDDLEWARE
// --------------------
Flight::before('route', function(&$route, &$args) {
    $path = Flight::request()->url;

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
        Flight::stop(); 
    }

    $token = substr($auth, 7);

    try {
        $data = JWT::decode($token);
        Flight::set('user', $data); 
    } catch (Exception $e) {
        Flight::json([
            'status' => 'error',
            'data' => null,
            'error' => 'Invalid token: ' . $e->getMessage()
        ], 401);
        Flight::stop(); 
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


$gc = new GradeController();
Flight::route('GET /grades', [$gc, 'getAll']);
Flight::route('GET /grades/@id', [$gc, 'getById']);
Flight::route('GET /grades/student/@idStudent', [$gc, 'getByStudent']);
Flight::route('GET /grades/studentReg/@registrationNumber', [$gc, 'getByStudentReg']);
Flight::route('POST /grades', [$gc, 'create']);
Flight::route('DELETE /grades/@id', [$gc, 'delete']);
Flight::route('GET /students/@idStudent/semester/@idSemester/releve', [$gc, 'getReleve']);

Flight::route('GET /grades/student/@idStudent/semester/@semesterName', function($idStudent, $semesterName) {
    GradeController::getByStudentSemester($idStudent, $semesterName);
});
Flight::route('GET /grades/student/@idStudent/year/@yearName', function($idStudent, $yearName) {
    GradeController::getByStudentYear($idStudent, $yearName);
});

$sc = new SemesterController();
Flight::route('GET /semesters', [$sc, 'getAll']);
Flight::route('GET /semesters/@idSemester', [$sc, 'getById']);
Flight::route('GET /semesters/year/@idAcademicYear', [$sc, 'getByYear']);
Flight::route('GET /students/semester/@idSemester/year/@year', [$sc, 'getBySemesterYear']);

?>
