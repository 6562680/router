<?php

namespace Gzhegow\Router\Domain\Container;

use Psr\Container\ContainerInterface;


/**
 * RouterContainerInterface
 */
interface RouterContainerInterface extends ContainerInterface
{
    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id) : bool;


    /**
     * @param string $id
     * @param mixed  $item
     *
     * @return static
     */
    public function set(string $id, $item);


    /**
     * @param null|object            $newthis
     * @param string|object|callable $callable
     * @param null|array             $parameters
     *
     * @return mixed
     */
    public function call(?object $newthis, $callable, array $parameters = null);


    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return mixed
     */
    public function autowireConstructor($objectOrClass, array $parameters = null) : array;

    /**
     * @param callable   $callable
     * @param null|array $parameters
     *
     * @return mixed
     */
    public function autowireCallable(callable $callable, array $parameters = null) : array;
}
