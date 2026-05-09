<?php

namespace Scy\Core\Routes;

class Route
{
    private static array $routes = [];
    private static array $prefixStack = [];

    // ─────────────────────────────
    // Public methods
    // ─────────────────────────────

    public static function get(string $uri, $action): void
    {
        self::add('GET', $uri, $action);
    }

    public static function post(string $uri, $action): void
    {
        self::add('POST', $uri, $action);
    }

    public static function any(string $uri, $action): void
    {
        self::add('ANY', $uri, $action);
    }

    // ─────────────────────────────
    // Core add logic
    // ─────────────────────────────

    private static function add(string $method, string $uri, $action): void
    {
        $prefix = implode('/', self::$prefixStack);

        $fullUri = '/' . trim($prefix . '/' . trim($uri, '/'), '/');

        self::$routes[] = [
            'method' => $method,
            'uri'    => $fullUri,
            'action' => $action,
        ];
    }

    // ─────────────────────────────
    // Prefix & Group
    // ─────────────────────────────

    public static function prefix(string $prefix): self
    {
        self::$prefixStack[] = trim($prefix, '/');
        return new self();
    }

    public function group(callable $callback): void
    {
        $callback();
        array_pop(self::$prefixStack);
    }

    // ─────────────────────────────
    // Dispatcher
    // ─────────────────────────────

    public static function dispatch(string $uri, string $method): bool
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');

        foreach (self::$routes as $route) {

            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            if ($route['uri'] === $uri) {
                self::run($route['action']);
                return true;
            }
        }

        // Debug (optional)
        // var_dump(self::$routes);

        return false;
    }

    // ─────────────────────────────
    // Execute action
    // ─────────────────────────────

    private static function run($action): void
    {
        if (is_callable($action)) {
            $action();
            return;
        }

        if (is_array($action)) {
            [$class, $method] = $action;

            if (!class_exists($class)) {
                throw new \Exception("Controller {$class} not found");
            }

            $controller = new $class();

            if (!method_exists($controller, $method)) {
                throw new \Exception("Method {$method} not found in {$class}");
            }

            $controller->$method();
            return;
        }

        throw new \Exception("Invalid route action");
    }

    // ─────────────────────────────
    // Debug helper (optional)
    // ─────────────────────────────

    public static function list(): array
    {
        return self::$routes;
    }

    public static function load(string $path = null): void
    {
        $path = $path ?? dirname(__DIR__, 1) . '/../Routes';

        $files = glob($path . '/*.php');

        if (empty($files)) {
            echo "⚠️ No route files found!\n";
            return;
        }

        foreach ($files as $file) {
            require_once $file;
        }

    }
}
