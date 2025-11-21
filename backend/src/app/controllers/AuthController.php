<?php
namespace app\controllers;

use Flight;
use app\helpers\JWT;
use app\models\UserModel;

class AuthController {

    public static function login() {
        $input = Flight::request()->data;
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        $user = UserModel::findByUsername($username);

        // Plain password check
        if (!$user || $user['password'] !== $password) {
            Flight::json([
                'status' => 'error',
                'data' => null,
                'error' => 'utilisateur non authentifie'
            ], 401);
            return;
        }

        $token = JWT::generate($user['id']);

        Flight::json([
            'status' => 'success',
            'data' => ['token' => $token],
            'error' => null
        ]);
    }
}
