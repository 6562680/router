<?php

namespace Gzhegow\Router\Domain\Handler;


/**
 * HandlerInterface
 */
interface HandlerInterface
{
    /**
     * @param mixed ...$arguments
     */
    public function handle(...$arguments);
}
