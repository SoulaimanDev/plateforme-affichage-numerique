<?php

/**
 * Gestionnaire de sessions sécurisé
 */
class sessionManager {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            
            session_start();
            
            if (!self::has('_token')) {
                session_regenerate_id(true);
                self::set('_token', bin2hex(random_bytes(32)));
            }
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function login($user) {
        self::start();
        session_regenerate_id(true);
        
        self::set('user_id', $user['id']);
        self::set('user_email', $user['email']);
        self::set('user_role', $user['role_name']);
        self::set('login_time', time());
        self::set('_token', bin2hex(random_bytes(32)));
    }

    public static function logout() {
        self::start();
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        session_destroy();
    }

    public static function isExpired($timeout = 3600) {
        $loginTime = self::get('login_time');
        return $loginTime && (time() - $loginTime) > $timeout;
    }

    public static function getCsrfToken() {
        if (!self::has('csrf_token')) {
            self::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return self::get('csrf_token');
    }

    public static function verifyCsrfToken($token) {
        return hash_equals(self::get('csrf_token', ''), $token);
    }
}