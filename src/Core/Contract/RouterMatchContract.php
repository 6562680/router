<?php

namespace Gzhegow\Router\Core\Contract;

use Gzhegow\Lib\Lib;
use Gzhegow\Router\Exception\LogicException;


class RouterMatchContract
{
    /**
     * @var array<int, bool>
     */
    public $idIndex = [];
    /**
     * @var array<string, bool>
     */
    public $nameIndex = [];
    /**
     * @var array<string, bool>
     */
    public $tagIndex = [];

    /**
     * @var array<string, bool>
     */
    public $pathIndex = [];
    /**
     * @var array<string, bool>
     */
    public $httpMethodIndex = [];


    private function __construct()
    {
    }


    /**
     * @return static
     */
    public static function from($from) : self
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
    public static function tryFrom($from, \Throwable &$last = null) : ?self
    {
        $last = null;

        Lib::php_errors_start($b);

        $instance = null
            ?? static::tryFromInstance($from)
            ?? static::tryFromArray($from);

        $errors = Lib::php_errors_end($b);

        if (null === $instance) {
            foreach ( $errors as $error ) {
                $last = new LogicException($error, null, $last);
            }
        }

        return $instance;
    }


    /**
     * @return static|null
     */
    public static function tryFromInstance($instance) : ?self
    {
        if (! is_a($instance, static::class)) {
            return Lib::php_error(
                [ 'The `from` should be instance of: ' . static::class, $instance ]
            );
        }

        return $instance;
    }

    /**
     * @return static
     */
    public static function tryFromArray($array) : ?self
    {
        if (! is_array($array)) {
            return Lib::php_error(
                [ 'The `from` should be array', $array ]
            );
        }

        $ids = [];
        $pathes = [];
        $methods = [];
        $names = [];
        $groups = [];

        if (isset($array[ 'id' ])) $ids[] = (array) $array[ 'id' ];
        if (isset($array[ 'ids' ])) $ids[] = (array) $array[ 'ids' ];

        if (isset($array[ 'path' ])) $pathes[] = (array) $array[ 'path' ];
        if (isset($array[ 'pathes' ])) $pathes[] = (array) $array[ 'pathes' ];

        if (isset($array[ 'httpMethod' ])) $methods[] = (array) $array[ 'httpMethod' ];
        if (isset($array[ 'httpMethods' ])) $methods[] = (array) $array[ 'httpMethods' ];

        if (isset($array[ 'name' ])) $names[] = (array) $array[ 'name' ];
        if (isset($array[ 'names' ])) $names[] = (array) $array[ 'names' ];

        if (isset($array[ 'tag' ])) $groups[] = (array) $array[ 'tag' ];
        if (isset($array[ 'tags' ])) $groups[] = (array) $array[ 'tags' ];

        $instance = new static();

        $instance->idIndex = Lib::array_index_int([], ...$ids);
        $instance->nameIndex = Lib::array_index_string([], ...$names);
        $instance->tagIndex = Lib::array_index_string([], ...$groups);

        $instance->pathIndex = Lib::array_index_string([], ...$pathes);
        $instance->httpMethodIndex = Lib::array_index_string([], ...$methods);

        return $instance;
    }
}
