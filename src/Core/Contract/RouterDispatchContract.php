<?php

namespace Gzhegow\Router\Core\Contract;

use Gzhegow\Lib\Lib;
use Gzhegow\Router\Core\Route\Struct\HttpMethod;
use Gzhegow\Router\Exception\LogicException;


class RouterDispatchContract
{
    /**
     * @var HttpMethod
     */
    public $httpMethod;
    /**
     * @var string
     */
    public $requestUri;


    private function __construct()
    {
    }


    /**
     * @return static
     */
    public static function from($from) // : static
    {
        $instance = static::tryFrom($from, $error);

        if (null === $instance) {
            throw $error;
        }

        return $instance;
    }

    /**
     * @return static|null
     */
    public static function tryFrom($from, \Throwable &$last = null) // : ?static
    {
        $last = null;

        Lib::php()->errors_start($b);

        $instance = null
            ?? static::tryFromInstance($from)
            ?? static::tryFromArray($from);

        $errors = Lib::php()->errors_end($b);

        if (null === $instance) {
            foreach ( $errors as $error ) {
                $last = new LogicException($error, $last);
            }
        }

        return $instance;
    }


    /**
     * @return static|null
     */
    public static function tryFromInstance($instance) // : ?static
    {
        if (! is_a($instance, static::class)) {
            return Lib::php()->error(
                [ 'The `from` should be instance of: ' . static::class, $instance ]
            );
        }

        return $instance;
    }

    /**
     * @return static|null
     */
    public static function tryFromArray($array) // : ?static
    {
        if (! is_array($array)) {
            return Lib::php()->error(
                [ 'The `from` should be array', $array ]
            );
        }

        [ $httpMethod, $requestUri ] = $array;

        if (null === ($_httpMethod = HttpMethod::tryFrom($httpMethod))) {
            return Lib::php()->error(
                [ 'The `from[0]` should be valid `httpMethod`', $httpMethod, $array ]
            );
        }

        if (null === ($_requestUri = Lib::parse()->path($requestUri))) {
            return Lib::php()->error(
                [ 'The `from[0]` should be valid `path`', $requestUri, $array ]
            );
        }

        $instance = new static();
        $instance->httpMethod = $_httpMethod;
        $instance->requestUri = $_requestUri;

        return $instance;
    }
}
