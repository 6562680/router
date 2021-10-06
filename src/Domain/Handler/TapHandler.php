<?php

namespace Gzhegow\Router\Domain\Handler;


/**
 * TapHandler
 */
class TapHandler implements HandlerInterface
{
    /**
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(...$arguments)
    {
        $result = $arguments[ 0 ] ?? null;

        return $result;
    }
}
