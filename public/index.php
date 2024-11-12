<?php

// Composer autoloader ve bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

try {
    // Route'ları çalıştır
    $router->dispatch();
} catch (Exception $e) {
    // Hata yakalama
    if (env('APP_DEBUG', false)) {
        throw $e;
    } else {
        if (request()->expectsJson()) {
            Response::json(['error' => 'Server Error'], 500);
        } else {
            View::make('errors.500');
        }
    }
}