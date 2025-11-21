<?php
namespace app\helpers;

class JWT {
    private static $secret = "CHANGE_THIS_SECRET"; // Change this to a strong secret

    /**
     * Generate a JWT token for a given user ID
     */
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

    /**
     * Validate a token
     * Returns payload if valid, false if invalid or expired
     */
    public static function validate($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        list($header, $payload, $signature) = $parts;

        // Check signature
        $expected = base64_encode(
            hash_hmac('sha256', "$header.$payload", self::$secret, true)
        );

        if ($signature !== $expected) return false;

        // Decode payload
        $data = json_decode(base64_decode($payload), true);
        if (!isset($data['exp']) || $data['exp'] < time()) return false;

        return $data;
    }

    /**
     * Decode a token without checking expiration or signature
     * Optional method if you want raw payload
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        $payload = $parts[1];
        return json_decode(base64_decode($payload), true);
    }
}
