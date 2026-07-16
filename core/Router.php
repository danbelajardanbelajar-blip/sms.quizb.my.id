<?php
class Router {
    private $routes = [];

    public function add($route, $controller, $action) {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    public function dispatch($url) {
        if (array_key_exists($url, $this->routes)) {
            $controllerName = $this->routes[$url]['controller'];
            $actionName = $this->routes[$url]['action'];

            $controller = new $controllerName();
            $controller->$actionName();
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
