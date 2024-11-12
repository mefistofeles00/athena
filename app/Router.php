<?php

class Router {
    private $routes = [];
    private $namedRoutes = [];
    private $middlewares = [];
    private $currentGroupPrefix = '';
    private $currentGroupMiddlewares = [];
    private static $cache = [];

    // Temel HTTP metodları için route ekleme
    public function get($path, $callback) {
        return $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        return $this->addRoute('POST', $path, $callback);
    }

    public function put($path, $callback) {
        return $this->addRoute('PUT', $path, $callback);
    }

    public function delete($path, $callback) {
        return $this->addRoute('DELETE', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $path = $this->currentGroupPrefix . $path;
        $middlewares = array_merge($this->currentGroupMiddlewares, $this->middlewares);

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];

        return $this;
    }

    // Named routes için
    public function name($name) {
        $lastRoute = end($this->routes);
        $this->namedRoutes[$name] = $lastRoute;
        return $this;
    }

    // Middleware ekleme
    public function middleware($middleware) {
        $this->middlewares[] = $middleware;
        return $this;
    }

    // Route grupları için
    public function group($prefix, $callback) {
        $previousPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix .= $prefix;

        $callback($this);

        $this->currentGroupPrefix = $previousPrefix;
        return $this;
    }

    // Resource route'ları için
    public function resource($prefix, $controller) {
        $this->get("/$prefix", "$controller@index")->name("$prefix.index");
        $this->get("/$prefix/create", "$controller@create")->name("$prefix.create");
        $this->post("/$prefix", "$controller@store")->name("$prefix.store");
        $this->get("/$prefix/{id}", "$controller@show")->name("$prefix.show");
        $this->get("/$prefix/{id}/edit", "$controller@edit")->name("$prefix.edit");
        $this->put("/$prefix/{id}", "$controller@update")->name("$prefix.update");
        $this->delete("/$prefix/{id}", "$controller@destroy")->name("$prefix.destroy");
        return $this;
    }

    // Route URL oluşturma
    public function route($name, $params = []) {
        if (!isset($this->namedRoutes[$name])) {
            throw new Exception("Route '$name' not found");
        }
        $path = $this->namedRoutes[$name]['path'];
        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }
        return $path;
    }

    // Cache sistemi
    public function cache() {
        $cacheFile = 'cache/routes.php';
        if (!is_dir('cache')) {
            mkdir('cache', 0777, true);
        }

        if (file_exists($cacheFile)) {
            self::$cache = require $cacheFile;
        } else {
            self::$cache = $this->routes;
            file_put_contents($cacheFile, '<?php return ' . var_export(self::$cache, true) . ';');
        }
    }

    // Subdomain routing
    public function domain($domain, $callback) {
        $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
        if ($domain === $currentDomain) {
            $callback($this);
        }
    }

    // Route'ları çalıştırma
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\/{([^}]+)}/', '/([^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // İlk eşleşmeyi kaldır

                // Middleware'leri çalıştır
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = new $middleware();
                    $middlewareInstance->handle();
                }

                // Controller@method formatını işle
                if (is_string($route['callback'])) {
                    list($controller, $method) = explode('@', $route['callback']);
                    // Namespace'i ekle
                    $controller = "App\\Controllers\\" . $controller;

                    if (!class_exists($controller)) {
                        throw new Exception("Controller not found: $controller");
                    }

                    $controller = new $controller();
                    return call_user_func_array([$controller, $method], $matches);
                }

                // Closure formatını işle
                return call_user_func_array($route['callback'], $matches);
            }
        }

        // 404 hatası
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}