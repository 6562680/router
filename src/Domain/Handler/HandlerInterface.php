<?php

namespace Gzhegow\Router\Domain\Handler;


/**
 * HandlerInterface
 */
interface HandlerInterface
{
    /**
     * @param mixed $payload
     * @param mixed ...$arguments
     */
    public function handle($payload, ...$arguments);
}
