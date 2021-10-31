<?php


namespace Gzhegow\Router\Service\ActionProcessor\Callback;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Service\ActionProcessor\CallableActionProcessor;


/**
 * CallableStaticActionProcessor
 */
class CallableStaticActionProcessor extends CallableActionProcessor
{
    /**
     * @param string|array $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        if (! ( is_string($source) && is_array($source) )) {
            return false;
        }

        if (! is_callable($source)) {
            return false;
        }

        if (null !== Helper::filterCallableArrayPublic($source)) {
            return false;
        }

        return true;
    }
}
