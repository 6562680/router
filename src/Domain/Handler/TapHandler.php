<?php

namespace Gzhegow\Router\Domain\Handler;


/**
 * TapHandler
 */
class TapHandler implements HandlerInterface
{
    /**
     * @param mixed $payload
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle($payload, ...$arguments)
    {
        return $payload;
    }
}
