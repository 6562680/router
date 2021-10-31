<?php


namespace Gzhegow\Router\Service\RouteCompiler;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Configuration\PatternCollection;


/**
 * EndpointRouteCompiler
 */
class EndpointRouteCompiler implements RouteCompilerInterface
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
     * @param Route $route
     *
     * @return void
     */
    public function compileRoute($route) : void
    {
        $endpoint = $route->getEndpoint();
        $endpointValue = $endpoint->getValue();

        $replacements = [];
        foreach ( $this->patternCollection->getPatterns() as $pattern => $regex ) {
            $search = preg_quote('{' . $pattern . '}', '/');
            $replacement = '(?P<' . $pattern . '>' . $regex . ')';

            $replacements[ $search ] = $replacement;
        }

        $endpointRegex = preg_quote($endpointValue, '/');
        $endpointRegex = strtr($endpointRegex, $replacements);

        if ($endpointRegex !== $endpointValue) {
            $endpointRegex = '/^' . $endpointRegex . '$/u';

            $endpoint->compile($endpointRegex);
        }
    }


    /**
     * @param Route $route
     *
     * @return bool
     */
    public function supportsRoute($route) : bool
    {
        if (! ( $route instanceof Route )) {
            return false;
        }

        $endpoint = $route->getEndpoint();
        $endpointPath = $endpoint->getValue();

        if (false === mb_stripos($endpointPath, '{')) {
            return false;
        }

        return true;
    }
}
