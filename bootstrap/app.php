<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Ortam değişkenlerini yükle
$dotenv =  \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Session başlat
session_start();

// Temel sabitleri tanımla
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', dirname(__DIR__) . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Helper fonksiyonları yükle
require_once APP_PATH . '/Helpers/View.php';
require_once APP_PATH . '/Helpers/Response.php';

// Genel helper fonksiyonlar
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

function config($key, $default = null) {
    static $config = [];

    if (empty($config)) {
        $files = glob(CONFIG_PATH . '/*.php');
        foreach ($files as $file) {
            $name = basename($file, '.php');
            $config[$name] = require $file;
        }
    }

    $parts = explode('.', $key);
    $value = $config;

    foreach ($parts as $part) {
        if (!isset($value[$part])) {
            return $default;
        }
        $value = $value[$part];
    }

    return $value;
}

function asset($path) {
    return '/assets/' . ltrim($path, '/');
}

function url($path = '') {
    return env('APP_URL') . '/' . ltrim($path, '/');
}

// Router'ı yükle
require_once APP_PATH . '/Router.php';
$router = new Router();

// CORS ayarlarını uygula
if (config('app.cors.enabled', false)) {
    Response::enableCORS(
        config('app.cors.allowed_origins', '*'),
        config('app.cors.allowed_methods', 'GET, POST, PUT, DELETE, OPTIONS')
    );
}

// Global middleware'leri uygula
$globalMiddlewares = config('app.middlewares', []);
foreach ($globalMiddlewares as $middleware) {
    $instance = new $middleware();
    $instance->handle();
}

// Route'ları yükle
require_once ROOT_PATH . '/routes/web.php';
if (file_exists(ROOT_PATH . '/routes/api.php')) {
    require_once ROOT_PATH . '/routes/api.php';
}