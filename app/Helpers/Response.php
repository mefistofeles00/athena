<?php
namespace App\Helpers;
class Response {
    private $headers = [];
    private $content = '';
    private $statusCode = 200;
    private $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error'
    ];

    // JSON response
    public static function json($data, $status = 200, $headers = []) {
        $response = new self();
        $response->setHeader('Content-Type', 'application/json');

        foreach ($headers as $key => $value) {
            $response->setHeader($key, $value);
        }

        $response->setStatusCode($status);
        $response->setContent(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $response->send();
    }

    // Success response
    public static function success($data = [], $message = 'Success', $status = 200) {
        return self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    // Error response
    public static function error($message = 'Error', $status = 400, $errors = []) {
        return self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    // Redirect response
    public static function redirect($url, $status = 302) {
        $response = new self();
        $response->setHeader('Location', $url);
        $response->setStatusCode($status);
        return $response->send();
    }

    // Back response (previous page)
    public static function back($fallback = '/') {
        $referer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        return self::redirect($referer);
    }

    // Download response
    public static function download($filepath, $filename = null) {
        if (!file_exists($filepath)) {
            return self::error('File not found', 404);
        }

        $response = new self();
        $filename = $filename ?? basename($filepath);

        $response->setHeader('Content-Type', mime_content_type($filepath));
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setHeader('Content-Length', filesize($filepath));
        $response->setContent(file_get_contents($filepath));

        return $response->send();
    }

    // View response
    public static function view($view, $data = [], $status = 200) {
        $response = new self();
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        $response->setStatusCode($status);

        // View dosyasını yükle
        ob_start();
        extract($data);
        include "views/$view.php";
        $content = ob_get_clean();

        $response->setContent($content);
        return $response->send();
    }

    // Header ekleme
    private function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    // Status code ayarlama
    private function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    // Content ayarlama
    private function setContent($content) {
        $this->content = $content;
        return $this;
    }

    // Response gönderme
    private function send() {
        // Status code ayarla
        http_response_code($this->statusCode);

        // Headers'ları ayarla
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Content'i gönder
        echo $this->content;
        exit;
    }

    // CORS ayarları
    public static function enableCORS($origins = '*', $methods = 'GET, POST, PUT, DELETE, OPTIONS') {
        $response = new self();
        $response->setHeader('Access-Control-Allow-Origin', $origins);
        $response->setHeader('Access-Control-Allow-Methods', $methods);
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // OPTIONS request için erken yanıt
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $response->send();
        }
    }

    // Cache control
    public static function setCache($seconds) {
        $response = new self();
        $response->setHeader('Cache-Control', "public, max-age=$seconds");
        return $response;
    }

    // No cache
    public static function noCache() {
        $response = new self();
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->setHeader('Pragma', 'no-cache');
        return $response;
    }
}