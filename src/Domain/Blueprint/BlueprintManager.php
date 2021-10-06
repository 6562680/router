<?php


namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Cors\CorsBuilder;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Exceptions\Runtime\UnexpectedValueException;


/**
 * BlueprintManager
 */
class BlueprintManager
{
    /**
     * @var BlueprintGroup[]
     */
    protected $groups = [];
    /**
     * @var Blueprint[]
     */
    protected $routes = [];


    /**
     * @param RouteLoaderInterface $routeLoader
     *
     * @return static
     */
    public function load(RouteLoaderInterface $routeLoader)
    {
        /** @var BlueprintGroup $group */

        $queue = $this->flushGroups();

        $stack = [];
        while ( null !== key($queue) ) {
            $stack[] = $group = array_shift($queue);

            if (! $routeLoader->supportsSource($source = $group->getSource())) {
                throw new UnexpectedValueException(
                    [ 'Invalid source: %s', $source ]
                );
            }

            $routeLoader->loadSource($source);

            $routes = $this->flushRoutes();
            $groups = $this->flushGroups();

            $group->addRoutes($routes);

            foreach ( $groups as $childGroup ) {
                $childGroup->setParent($group);

                $queue[] = $childGroup;
            }
        }

        $routes = [];
        end($stack);
        while ( null !== key($stack) ) {
            $group = current($stack);

            if ($parentGroup = $group->getParent()) {
                $parentGroup->addRoutes($group->flushRoutes());

            } else {
                foreach ( $group->flushRoutes() as $route ) {
                    $routes[] = $route;
                }
            }

            prev($stack);
        }

        $this->routes = $routes;

        return $this;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function get($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('GET', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function post($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('POST', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function put($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('PUT', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function patch($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('PATCH', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function delete($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('DELETE', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function purge($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('PURGE', $endpoint, $action, $name);
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function head($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('HEAD', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function options($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('OPTIONS', $endpoint, $action, $name);
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function trace($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('TRACE', $endpoint, $action, $name);
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function connect($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('CONNECT', $endpoint, $action, $name);
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function cli($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('CLI', $endpoint, $action, $name);
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function sock($endpoint, $action, string $name = null) : Blueprint
    {
        return $this->route('SOCK', $endpoint, $action, $name);
    }


    /**
     * @param null|string $namespace
     *
     * @return BlueprintGroup
     */
    public function namespace(?string $namespace)
    {
        $group = $this->group(null);
        $group->namespace($namespace);

        return $group;
    }


    /**
     * @param null|string $endpoint
     *
     * @return BlueprintGroup
     */
    public function endpoint(?string $endpoint)
    {
        $group = $this->group(null);
        $group->endpoint($endpoint);

        return $group;
    }

    /**
     * @param null|string $endpointRegex
     *
     * @return BlueprintGroup
     */
    public function endpointRegex(?string $endpointRegex)
    {
        $group = $this->group(null);
        $group->endpointRegex($endpointRegex);

        return $group;
    }


    /**
     * @param null|string $namespace
     *
     * @return BlueprintGroup
     */
    public function name(?string $namespace)
    {
        $group = $this->group(null);
        $group->name($namespace);

        return $group;
    }

    /**
     * @param null|string $description
     *
     * @return BlueprintGroup
     */
    public function description(?string $description)
    {
        $group = $this->group(null);
        $group->description($description);

        return $group;
    }


    /**
     * @param null|array $bindings
     *
     * @return BlueprintGroup
     */
    public function bindings(?array $bindings)
    {
        $group = $this->group(null);
        $group->bindings($bindings);

        return $group;
    }

    /**
     * @param mixed|iterable $middlewares
     *
     * @return BlueprintGroup
     */
    public function middlewares($middlewares)
    {
        $group = $this->group(null);
        $group->middlewares($middlewares);

        return $group;
    }

    /**
     * @param string|iterable $tags
     *
     * @return BlueprintGroup
     */
    public function tags($tags)
    {
        $group = $this->group(null);
        $group->tags($tags);

        return $group;
    }


    /**
     * @param null|CorsBuilder $cors
     *
     * @return BlueprintGroup
     */
    public function cors(?CorsBuilder $cors)
    {
        $group = $this->group(null);
        $group->cors($cors);

        return $group;
    }


    /**
     * @param string      $method
     * @param string      $endpoint
     * @param mixed       $action
     * @param null|string $name
     *
     * @return Blueprint
     */
    public function route(string $method, string $endpoint, $action, string $name = null) : Blueprint
    {
        $this->routes[] = $route = new Blueprint();

        $route->method($method);
        $route->endpoint($endpoint);
        $route->action($action);
        $route->name($name);

        return $route;
    }

    /**
     * @return BlueprintGroup
     */
    public function group($source)
    {
        $this->groups[] = $group = new BlueprintGroup();

        $group->group($source);

        return $group;
    }


    /**
     * @return Blueprint[]
     */
    public function flushRoutes() : array
    {
        $routes = $this->routes;
        $this->routes = [];

        return $routes;
    }

    /**
     * @return BlueprintGroup[]
     */
    public function flushGroups() : array
    {
        $groups = $this->groups;
        $this->groups = [];

        return $groups;
    }
}
