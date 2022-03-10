<?php

namespace Core\Routing;

class Container
{
    private $routes;

    public static function newController(string $controllerName)
    {
        $controller = "App\\Controllers\\".$controllerName;

        return new $controller();
    }
}
