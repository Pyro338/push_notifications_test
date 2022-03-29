<?php

namespace core;

class baseClass extends modelsBootstrap
{
    public $request;
    private $routes;

    public function __construct()
    {
        parent::__construct();
        $this->routes  = require(__DIR__ . '/../routes.php');
        $this->request = new Request();
    }

    public function run()
    {
        try {
            $route = $this->request->method . $this->request->endpoint;
            if (isset($this->routes[$route])) {
                $controllerName = '\controllers\\' . ucfirst($this->routes[$route]['controller']);
                $methodName     = $this->routes[$route]['method'];
                $controller     = new $controllerName();
                $response       = $controller->$methodName((array)$this->request->data);
                Response::set($response);
            }
        } catch (\Exception $exception) {
            Response::set($exception->getMessage(), 500, $exception);
        }

        Response::set("Incorrect endpoint", 400);
    }
}