<?php


namespace Gzhegow\Router\Service\RouteCompiler;

use Gzhegow\Router\Domain\Blueprint\Blueprint;
use Gzhegow\Router\Domain\Configuration\PatternCollection;


/**
 * PatternRouteCompiler
 */
class PatternRouteCompiler implements RouteCompilerInterface
{
    /**
     * @var PatternCollection
     */
    protected $patternCollection;


    /**
     * Constructor
     *
     * @param PatternCollection $patternCollection
     */
    public function __construct(PatternCollection $patternCollection)
    {
        $this->patternCollection = $patternCollection;
    }


    /**
     * @param Blueprint $route
     *
     * @return void
     */
    public function compileRoute($route) : void
    {
        $endpoint = $route->getEndpoint();

        $replacements = [];
        foreach ( $this->patternCollection->getPatterns() as $pattern => $regex ) {
            $search = preg_quote('{' . $pattern . '}', '/');
            $replacement = '(?P<' . $pattern . '>' . $regex . ')';

            $replacements[ $search ] = $replacement;
        }

        $endpointRegex = preg_quote($route->getEndpoint(), '/');
        $endpointRegex = strtr($endpointRegex, $replacements);

        if ($endpointRegex !== $endpoint) {
            $endpointRegex = '/^' . $endpointRegex . '$/u';

            $route->endpointRegex($endpointRegex);
        }
    }


    /**
     * @param Blueprint $route
     *
     * @return bool
     */
    public function supportsRoute($route) : bool
    {
        if (! ( $route instanceof Blueprint )) {
            return false;
        }

        if (false === mb_stripos($route->getEndpoint(), '{')) {
            return false;
        }

        return true;
    }
}
