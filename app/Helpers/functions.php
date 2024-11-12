<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
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
}

if (!function_exists('asset')) {
    function asset($path) {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        return env('APP_URL') . '/' . ltrim($path, '/');
    }
}