<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Routing\Route;
use Core\Routing\Container;

class Router {
    private $request;
    private $routes;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->routes = Route::getRoutes();
    }
    
    private function reloadRoutes()
    {
        $this->routes = Route::getRoutes();
    }

    private function callControllerAction($callback, Request $r, $p = null)
    {
        $exploded = explode("@", $callback);

        $action = $exploded[1];

        $controller = Container::newController($exploded[0]);

        return $controller->$action($resquest = $r, $params = $p);
    }

    public function run()
    {
        $this->reloadRoutes();
        
        $methods = ['GET', 'POST', 'PUT', 'DELETE'];

        if (!in_array($this->request->getMethod(), $methods)) {
            return;
        }

        foreach ($this->routes as $method => $paths) {
            foreach ($this->routes[$method] as $path => $callbackInfo) {

                $pattern_regex = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $path); // pattern routes[method][path]
    
                $pattern_regex = "#^" . trim($pattern_regex) . "$#";
                
                if (!preg_match($pattern_regex, rtrim($this->request->path(), "/"), $matches)) {
                    continue;
                };
    
                if ($callbackInfo['isAjax'] && !$this->request->isAjax()){
                    return;
                }
    
                foreach ($matches as $key => $value) {
                    if (is_numeric($key)) {
                        unset($matches[$key]);
                    }
                }


                $callback = $callbackInfo['callback'];

                if (is_callable($callback)) {
                    return $callback($resquest = $this->request, $params = (object)$matches);
                }else{
                    return $this->callControllerAction($callback, $this->request, $params = (object)$matches);
                }
            }
        }
        echo "nao encontrado 404";
        return;
    }
}

