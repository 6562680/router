<?php

namespace Gzhegow\Router\Handler\Demo\Fallback;


class DemoFallback
{
    public function __invoke(\Throwable $e, $input = null, $context = null)
    {
        var_dump(__METHOD__);

        return null;
    }
}
