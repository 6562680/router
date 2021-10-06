<?php


namespace Gzhegow\Router\Service\ActionProcessor\CallableActionProcessor;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Service\ActionProcessor\CallableActionProcessor;


/**
 * CallableDynamicActionProcessor
 */
class CallableDynamicActionProcessor extends CallableActionProcessor
{
    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return ( is_object($source) && is_callable($source) )
            || Helper::filterCallableArrayPublic($source);
    }
}
