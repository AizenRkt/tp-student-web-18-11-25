<?php
namespace app\helpers;

class JWT {
    private static $secret = "CHANGE_THIS_SECRET";

    public static function generate($userId) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'id' => $userId,
            'exp' => time() + 3600 // token valid 1 hour
        ]));

        $signature = base64_encode(
            hash_hmac('sha256', "$header.$payload", self::$secret, true)
        );

        return "$header.$payload.$signature";
    }

    public static function validate($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        list($h, $p, $s) = $parts;

        $expected = base64_encode(
            hash_hmac('sha256', "$h.$p", self::$secret, true)
        );

        if ($s !== $expected) return false;

        $payload = json_decode(base64_decode($p), true);

        if (!isset($payload['exp']) || $payload['exp'] < time()) return false;

        return $payload;
    }
}
