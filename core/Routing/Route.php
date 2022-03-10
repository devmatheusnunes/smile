<?php

namespace Core\Routing;

class Route {

    private static $routes = [];

    public static function getRoutes()
    {
        return self::$routes;
    }
    
    public static function get(string $pattern, $callback, bool $require_ajax = false)
    {
        return self::addRoute('GET', $pattern, ['callback' => $callback, 'isAjax' => $require_ajax]);
    }

    public static function post(string $pattern, $callback, bool $require_ajax = false)
    {
        return self::addRoute('POST', $pattern, ['callback' => $callback, 'isAjax' => $require_ajax]);
    }

    public static function put(string $pattern, $callback, bool $require_ajax = false)
    {
        return self::addRoute('PUT', $pattern, ['callback' => $callback, 'isAjax' => $require_ajax]);
    }

    public static function delete(string $pattern, $callback, bool $require_ajax = false)
    {
        return self::addRoute('DELETE', $pattern, ['callback' => $callback, 'isAjax' => $require_ajax]);
    }

    private static function addRoute(string $method, string $path, array $callbackInfo) // [callback, isAjax, paramters]
    {
        $path = rtrim($path, "/");
        
        if (isset(self::$routes[$method][$path])) {
            return;
        }
        self::$routes[$method][$path] = $callbackInfo;
    }

}

