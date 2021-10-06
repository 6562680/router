<?php

namespace Gzhegow\Router\Domain\Cors;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * CorsMiddleware
 */
class CorsMiddleware
{
    /**
     * @param HandlerInterface $next
     * @param Route            $route
     * @param mixed            ...$arguments
     *
     * @return null|int|mixed
     */
    public function __invoke(HandlerInterface $next, Route $route, ...$arguments)
    {
        $isPreflight = 'OPTIONS' === strtoupper($_SERVER[ 'REQUEST_METHOD' ] ?? '');

        return null
            ?? ( $isPreflight ? $this->handlePreflight($next, $route, ...$arguments) : null )
            ?? $this->handle($next, $route, ...$arguments);
    }


    /**
     * @param HandlerInterface $next
     * @param Route            $route
     * @param mixed            ...$arguments
     *
     * @return mixed
     */
    protected function handle(HandlerInterface $next, Route $route, ...$arguments)
    {
        if (! $cors = $route->getCors()) {
            $result = $next->handle(...$arguments) ?? 0;

            return $result;
        }

        $headers = [
            'Access-Control-Allow-Origin' => null,
        ];

        if ($regexes = $cors->getAllowOrigins() ?? []) {
            $httpOrigin = $_SERVER[ 'HTTP_ORIGIN' ] ?? null;

            foreach ( $regexes as $regex ) {
                if (preg_match('^' . $regex . '$', $httpOrigin)) {
                    $headers[ 'Access-Control-Allow-Origin' ] = $httpOrigin;

                    break;
                }
            }
        }

        foreach ( $headers as $header => $value ) {
            if (null !== $value) {
                header($header . ': ' . $value);
            }
        }

        return $next->handle(...$arguments);
    }

    /**
     * @param HandlerInterface $next
     * @param Route            $route
     * @param mixed            ...$arguments
     *
     * @return null|int
     */
    protected function handlePreflight(HandlerInterface $next, Route $route, ...$arguments)
    {
        if (! $cors = $route->getCors()) {
            $result = $next->handle(...$arguments) ?? 0;

            return $result;
        }

        $headers = [
            'Access-Control-Allow-Methods'     => null,
            'Access-Control-Allow-Credentials' => null,
            'Access-Control-Allow-Origin'      => null,
            'Access-Control-Allow-Headers'     => null,
            'Access-Control-Expose-Headers'    => null,
        ];

        $headers[ 'Access-Control-Allow-Methods' ] = $route->getMethod() . ',OPTIONS';

        if ($cors->getAllowCredentials()) {
            $headers[ 'Access-Control-Allow-Credentials' ] = 'true';
        }

        if ($regexes = $cors->getAllowOrigins() ?? []) {
            $httpOrigin = $_SERVER[ 'HTTP_ORIGIN' ] ?? null;

            foreach ( $regexes as $regex ) {
                if (preg_match('^' . $regex . '$', $httpOrigin)) {
                    $headers[ 'Access-Control-Allow-Origin' ] = $httpOrigin;

                    break;
                }
            }
        }

        $regexesAllowHeaders = $cors->getAllowHeaders() ?? [];
        $regexesExposeHeaders = $cors->getExposeHeaders() ?? [];

        if ($regexesAllowHeaders || $regexesExposeHeaders) {
            $httpAccessControlRequestHeaders = $_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' ] ?? '';

            $array = array_map('trim',
                explode(',', $httpAccessControlRequestHeaders)
            );

            $valuesAllowHeaders = [];
            foreach ( $regexesAllowHeaders as $regex ) {
                foreach ( $array as $item ) {
                    if (preg_match('/^' . $regex . '$/', $item)) {
                        $valuesAllowHeaders[] = $item;
                    }
                }
            }

            $valuesExposeHeaders = [];
            foreach ( $regexesExposeHeaders as $regex ) {
                foreach ( $array as $item ) {
                    if (preg_match('/^' . $regex . '$/', $item)) {
                        $valuesExposeHeaders[] = $item;
                    }
                }
            }

            if ($valuesAllowHeaders = implode(',', $valuesAllowHeaders)) {
                $headers[ 'Access-Control-Allow-Headers' ] = $valuesAllowHeaders;
            }

            if ($valuesExposeHeaders = implode(',', $valuesExposeHeaders)) {
                $headers[ 'Access-Control-Expose-Headers' ] = $valuesExposeHeaders;
            }
        }

        foreach ( $headers as $header => $value ) {
            if (null !== $value) {
                header($header . ': ' . $value);
            }
        }

        return 0;
    }
}
