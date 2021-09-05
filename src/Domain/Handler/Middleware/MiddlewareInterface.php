<?php

namespace Gzhegow\Router\Domain\Handler\Middleware;

use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * MiddlewareInterface
 */
interface MiddlewareInterface extends HandlerInterface
{
    /**
     * @return HandlerInterface
     */
    public function getNext() : HandlerInterface;
}
