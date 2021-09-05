<?php

namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Compiler\RouteCompilerInterface;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * RouteCollectionBuilder
 */
class RouteCollectionBuilder
{
    /**
     * @var RouteCompilerInterface
     */
    protected $routeCompiler;

    /**
     * @var BlueprintRouteGroup
     */
    protected $routeGroup;
    /**
     * @var BlueprintRouteGroup
     */
    protected $routeGroupBlueprint;


    /**
     * Constructor
     */
    public function __construct(RouteCompilerInterface $routeCompiler)
    {
        $this->routeCompiler = $routeCompiler;

        $this->routeGroup = new BlueprintRouteGroup($configuration);
        $this->routeGroupBlueprint = null;
    }


    /**
     * @return RouteCollection
     */
    public function build() : RouteCollection
    {
        $routeCompiler = $this->configuration->getRouteCompiler();

        $routeCollection = new RouteCollection();

        $routeBlueprints = $this->routeGroup->flush();

        foreach ( $routeBlueprints as $routeBlueprint ) {
            $route = $routeCompiler->compile($routeBlueprint);

            $routeCollection->addRoute($route);
        }

        return $routeCollection;
    }


    /**
     * @return BlueprintRoute
     */
    public function newRouteBlueprint() : BlueprintRoute
    {
        return new BlueprintRoute($this->configuration);
    }

    /**
     * @return BlueprintRouteGroup
     */
    public function newRouteGroupBlueprint() : BlueprintRouteGroup
    {
        return new BlueprintRouteGroup($this->configuration);
    }


    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function get(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('GET', $endpoint, $action);
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function post(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('POST', $endpoint, $action);
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function put(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('PUT', $endpoint, $action);
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function patch(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('PATCH', $endpoint, $action);
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function delete(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('DELETE', $endpoint, $action);
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function purge(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('PURGE', $endpoint, $action);
    }


    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function options(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('OPTIONS', $endpoint, $action);
    }


    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function cli(string $endpoint, $action) : BlueprintRoute
    {
        return $this->route('CLI', $endpoint, $action);
    }


    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function route(string $method, string $endpoint, $action) : BlueprintRoute
    {
        $routeBlueprint = $this->newRouteBlueprint()
            ->method($method)
            ->endpoint($endpoint)
            ->action($action);

        $this->routeGroup->addBlueprint($routeBlueprint);

        return $routeBlueprint;
    }

    /**
     * @param string|callable|object|mixed $group
     *
     * @return static
     */
    public function group($group)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroup = $this->routeGroupBlueprint;
        $this->routeGroupBlueprint = null;

        // @todo

        if (null === $result) {
            throw new InvalidArgumentException(
                [ 'Invalid groupLoader: %s', $group ]
            );
        }

        $routeGroupParent = $this->routeGroup->getParent();

        $this->routeGroup->mount($routeGroupParent);
        $this->routeGroup = $routeGroupParent;

        return $this;
    }


    /**
     * @param string $endpoint
     *
     * @return static
     */
    public function endpoint(string $endpoint)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroupBlueprint->endpoint($endpoint);

        return $this;
    }


    /**
     * @param string $namespace
     *
     * @return static
     */
    public function namespace(string $namespace)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroupBlueprint->namespace($namespace);

        return $this;
    }


    /**
     * @param string $name
     *
     * @return static
     */
    public function name(string $name)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroupBlueprint->name($name);

        return $this;
    }


    /**
     * @param array $bindings
     *
     * @return static
     */
    public function bindings(array $bindings)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroupBlueprint->bindings($bindings);

        return $this;
    }

    /**
     * @param array $tags
     *
     * @return static
     */
    public function tags(array $tags)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroupBlueprint->tags($tags);

        return $this;
    }


    /**
     * @param string|string[] $middlewares
     *
     * @return static
     */
    public function middlewares(array $middlewares)
    {
        if (! $this->routeGroupBlueprint) {
            $this->routeGroupBlueprint = $this->newRouteGroupBlueprint();
            $this->routeGroupBlueprint->withParent($this->routeGroup);
        }

        $this->routeGroupBlueprint->middlewares($middlewares);

        return $this;
    }
}
