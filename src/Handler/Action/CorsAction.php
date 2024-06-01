<?php

namespace Gzhegow\Router\Handler\Action;

class CorsAction
{
    public function __invoke()
    {
        if (isset($_SERVER[ 'HTTP_ORIGIN' ])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if (isset($_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_METHOD' ])) {
            header("Access-Control-Allow-Methods: OPTIONS, {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']}");
        }

        if (isset($_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' ])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            header("Access-Control-Expose-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
    }
}
