<?php

use app\controllers\Controller;
use app\helpers\JWT;
use Flight;

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

// --------------------
// Optional example routes
// --------------------
// Flight::route('/hello-world/@name', function($name) {
//     echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
// });

// Flight::group('/api', function() {
//     // Example API routes here
// });

?>
