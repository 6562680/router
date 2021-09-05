<?php


namespace Gzhegow\Router\Domain\Compiler;

use Gzhegow\Router\Utils;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\BlueprintRoute;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * PatternRouteCompiler
 */
class PatternRouteCompiler implements RouteCompilerInterface
{
    /**
     * @var \Gzhegow\Router\Domain\Configuration\Configuration
     */
    protected $configuration;


    /**
     * Constructor
     *
     * @param \Gzhegow\Router\Domain\Configuration\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param BlueprintRoute $routeBlueprint
     *
     * @return Route
     */
    public function compile(BlueprintRoute $routeBlueprint) : Route
    {
        $route = new Route(
            $routeBlueprint->getMethod(),
            $routeBlueprint->getEndpoint(),
            $routeBlueprint->getAction(),

            $routeBlueprint->getName(),

            $routeBlueprint->getBindings(),
            $routeBlueprint->getMiddlewares(),
            $routeBlueprint->getTags()
        );

        $replacements = [];
        foreach ( $this->configuration->getEndpointPatterns() as $pattern => $regex ) {
            $search = preg_quote('{' . $pattern . '}', '/');
            $replacement = '(?P<' . $pattern . '>' . $regex . ')';

            $replacements[ $search ] = $replacement;
        }

        $endpoint = $routeBlueprint->getEndpoint();

        $endpointCompiled = preg_quote($endpoint, '/');

        $endpointCompiled = strtr($endpointCompiled, $replacements);

        if (null === Utils::filterRegexShort($endpointCompiled)) {
            throw new InvalidArgumentException(
                [ 'Invalid endpoint: %s', $endpointCompiled ]
            );
        }

        $flags = Utils::filterStrUtf8($endpointCompiled)
            ? 'u' : '';

        $route->withEndpointCompiled('/' . $endpointCompiled . '/' . $flags);

        return $route;
    }
}
