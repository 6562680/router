<?php

namespace Gzhegow\Router\Demo\Handler\Fallback;


class DemoLogicExceptionFallback
{
    public function __invoke(\Throwable $e, $input = null, $context = null, $state = null)
    {
        if (! is_a($e, \LogicException::class)) return null;

        echo __METHOD__ . PHP_EOL;

        return __METHOD__ . ' result.';
    }
}
