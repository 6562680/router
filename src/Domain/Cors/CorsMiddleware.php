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
     * @param                  ...$arguments
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
     * @param                  ...$arguments
     *
     * @return mixed
     */
    protected function handle(HandlerInterface $next, Route $route, ...$arguments)
    {
        if (! $cors = $route->getCors()) {
            $result = $next->handle(...$arguments) ?? 0;

            return $result;
        }

        if ($regexes = $cors->getAllowOrigins() ?? []) {
            $headerOrigin = $_SERVER[ 'HTTP_ORIGIN' ] ?? null;

            foreach ( $regexes as $regex ) {
                if (preg_match('^' . $regex . '$', $headerOrigin)) {
                    header('Access-Control-Allow-Origin: ' . $headerOrigin);

                    break;
                }
            }
        }

        return $next->handle(...$arguments);
    }

    /**
     * @param HandlerInterface $next
     * @param Route            $route
     * @param                  ...$arguments
     *
     * @return null|int
     */
    protected function handlePreflight(HandlerInterface $next, Route $route, ...$arguments)
    {
        if (! $cors = $route->getCors()) {
            $result = $next->handle(...$arguments) ?? 0;

            return $result;
        }

        header('Access-Control-Allow-Methods: ', $route->getMethod() . ',OPTIONS');

        if ($cors->getAllowCredentials()) {
            header('Access-Control-Allow-Credentials: true');
        }

        if ($regexes = $cors->getAllowOrigins() ?? []) {
            $headerOrigin = $_SERVER[ 'HTTP_ORIGIN' ] ?? null;

            foreach ( $regexes as $regex ) {
                if (preg_match('^' . $regex . '$', $headerOrigin)) {
                    header('Access-Control-Allow-Origin: ' . $headerOrigin);

                    break;
                }
            }
        }

        if ($regexes = $cors->getAllowOrigins() ?? []) {
            $headerOrigin = $_SERVER[ 'HTTP_ORIGIN' ] ?? null;

            foreach ( $regexes as $regex ) {
                if (preg_match('/^' . $regex . '$/', $headerOrigin)) {
                    header('Access-Control-Allow-Origin: ' . $headerOrigin);

                    break;
                }
            }
        }

        if ($regexes = $cors->getAllowHeaders() ?? []) {
            $corsHeaders = $_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' ] ?? '';
            $corsHeaders = array_map('trim', explode(',', $corsHeaders));

            $headers = [];
            foreach ( $regexes as $regex ) {
                foreach ( $corsHeaders as $corsHeader ) {
                    if (preg_match('/^' . $regex . '$/', $corsHeader)) {
                        $headers[] = $corsHeader;
                    }
                }
            }

            if ($headers = implode(',', $headers)) {
                header('Access-Control-Allow-Headers: ' . $headers);
            }
        }

        if ($regexes = $cors->getExposeHeaders() ?? []) {
            $corsHeaders = $_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' ] ?? '';
            $corsHeaders = array_map('trim', explode(',', $corsHeaders));

            $headers = [];
            foreach ( $regexes as $regex ) {
                foreach ( $corsHeaders as $corsHeader ) {
                    if (preg_match('/^' . $regex . '$/', $corsHeader)) {
                        $headers[] = $corsHeader;
                    }
                }
            }

            if ($headers = implode(',', $headers)) {
                header('Access-Control-Expose-Headers: ' . $headers);
            }
        }

        return 0;
    }
}
