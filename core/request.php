<?php
/**
 * Created by PhpStorm.
 * User: Nikolay
 * Date: 21.10.2019
 * Time: 11:00
 */

namespace core;

class Request extends RequestResponse
{
    public $data;
    public $method;
    public $endpoint;

    private $routes;

    public function __construct()
    {
        $this->routes = require(__DIR__ . '/../routes.php');

        $this->data   = (array)json_decode(file_get_contents('php://input'));
        $this->method = $_SERVER['REQUEST_METHOD'];

        $replaced = strstr($_SERVER['REQUEST_URI'], '?', true);

        $url            = $replaced ? $replaced : $_SERVER['REQUEST_URI'];
        $this->endpoint = str_replace('/integration-api', '', $url);

        if ($this->method === 'GET') {
            $this->data = $_GET;
        }

        $this->__changeParameters();
    }

    public function get($param = null)
    {
        if (!$param) {
            return $this->data;
        }
        if (isset($this->data[$param])) {
            return $this->data[$param];
        }
    }

    public function validateRequiredParams($params = [])
    {
        $missed_params = [];

        $data = (array)$this->data;

        foreach ($params as $param) {
            if (!isset($data[$param])) {
                $missed_params[] = $param;
            }
        }

        if ($missed_params) {
            $message = "Required fields: [" . implode(', ', $missed_params) . '] are missed';
            Response::set($message, 400);
        }

    }

    public function filter($fields = [])
    {
        return self::format($this->data, $fields);
    }

    private function __changeParameters()
    {
        $exploded_uri = explode('/', $this->__cutUri($this->endpoint));

        $params = [];

        if (!$route = $this->__findRoute($exploded_uri)) {
            return false;
        }

        $exploded_route = explode('/', $this->__cutUri($route));

        foreach ($exploded_route as $key => $param) {
            $result = preg_match('/{(.*?)}/', $param, $matches);

            if ($result === 1 && isset($matches[1])) {
                $params[$matches[1]] = $exploded_uri[$key];
                $exploded_uri[$key]  = $param;
            }
        }

        $this->data = array_merge($this->data, $params);

        $this->endpoint = implode('/', $exploded_uri);
    }

    private function __findRoute($exploded_uri)
    {
        foreach ($this->routes as $route => $data) {
            $exploded_route         = explode('/', str_replace($this->method, '', $this->__cutUri($route)));
            $exploded_uri_for_route = $exploded_uri;

            foreach ($exploded_route as $key => $param) {
                $result = preg_match('/{(.*?)}/', $param, $matches);

                if ($result === 1 && isset($matches[1])) {
                    unset($exploded_route[$key], $exploded_uri_for_route[$key]);
                }
            }

            if ($exploded_route === $exploded_uri_for_route) {
                return $route;
            }
        }
    }

    private function __cutUri($str)
    {
        if ($str{strlen($str) - 1} == '/') {
            $str = substr($str, 0, -1);
        }

        return $str;
    }

}