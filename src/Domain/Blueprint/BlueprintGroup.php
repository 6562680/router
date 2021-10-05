<?php


namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Cors\CorsBuilder;


/**
 * BlueprintGroup
 */
class BlueprintGroup extends AbstractBlueprint
{
    /**
     * @var BlueprintGroup
     */
    protected $parent;

    /**
     * @var Blueprint[]
     */
    protected $routes = [];

    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var mixed
     */
    protected $source;


    /**
     * @return null|BlueprintGroup
     */
    public function getParent() : ?BlueprintGroup
    {
        return $this->parent;
    }


    /**
     * @return Blueprint[]
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }


    /**
     * @return null|string
     */
    public function getNamespace() : ?string
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }


    /**
     * @param null|BlueprintGroup $parent
     *
     * @return static
     */
    public function setParent(?BlueprintGroup $parent)
    {
        $this->parent = $parent;

        return $this;
    }


    /**
     * @param null|Blueprint|iterable $routes
     *
     * @return static
     */
    public function setRoutes($routes) : BlueprintGroup
    {
        if (null === $routes) {
            $this->routes = [];

        } else {
            $this->addRoutes($routes);
        }

        return $this;
    }

    /**
     * @param null|Blueprint|iterable $routes
     *
     * @return static
     */
    public function addRoutes($routes)
    {
        $routes = is_iterable($routes)
            ? $routes
            : [ $routes ];

        foreach ( $routes as $route ) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * @param Blueprint $route
     *
     * @return static
     */
    public function addRoute(Blueprint $route)
    {
        $this->routes[] = $route;

        return $this;
    }


    /**
     * @param null|string $namespace
     *
     * @return static
     */
    public function namespace(?string $namespace)
    {
        $this->namespace = ltrim($namespace, '\\');

        return $this;
    }

    /**
     * @param mixed $source
     *
     * @return static
     */
    public function group($source)
    {
        $this->source = $source;

        return $this;
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
     * @return Blueprint[]
     */
    public function flushRoutes() : array
    {
        $routes = $this->routes;
        $this->routes = [];

        foreach ( $routes as $route ) {
            $this->applyToRoute($route);
            $this->applyCorsToRoute($route);
        }

        return $routes;
    }


    /**
     * @param Blueprint $route
     *
     * @return static
     */
    protected function applyToRoute(Blueprint $route)
    {
        $action = $route->getAction();

        if (( null !== $this->namespace )
            && is_string($action)
            && ! is_callable($action)
        ) {
            $action = $this->namespace . '\\' . ltrim($action, '\\');

            $route->action($action);
        }

        if (null !== $this->endpoint) {
            $route->endpoint($this->endpoint . $route->getEndpoint());
        }

        if (null !== $this->endpointRegex) {
            $route->endpointRegex($this->endpointRegex . $route->getEndpointRegex());
        }

        if (null !== $this->name) {
            $route->name($this->name . $route->getName());
        }

        if (null !== $this->description) {
            $route->description($this->description . $route->getDescription());
        }

        if ($this->bindings) {
            $route->bindings([]
                + $route->getBindings()
                + $this->bindings
            );
        }

        if ($this->middlewares) {
            $route->middlewares(
                array_unique(array_merge($this->middlewares,
                    $route->getMiddlewares(),
                ))
            );
        }

        if ($this->tags) {
            $route->tags(
                array_unique(array_merge($this->tags,
                    $route->getTags(),
                ))
            );
        }

        return $this;
    }

    /**
     * @param Blueprint $routeBlueprint
     *
     * @return static
     */
    protected function applyCorsToRoute(Blueprint $routeBlueprint)
    {
        if (null !== $this->cors) {
            $cors = null
                ?? $routeBlueprint->getCors()
                ?? new CorsBuilder();

            if (null !== ( $groupValue = $this->cors->getAllowOrigins() )) {
                $cors->allowOrigins(
                    array_unique(array_merge(
                        $groupValue,
                        $cors->getAllowOrigins() ?? []
                    ))
                );
            }

            if (null !== ( $groupValue = $this->cors->getAllowHeaders() )) {
                $cors->allowHeaders(
                    array_unique(array_merge(
                        $groupValue,
                        $cors->getAllowHeaders() ?? []
                    ))
                );
            }

            if (null !== ( $groupValue = $this->cors->getExposeHeaders() )) {
                $cors->exposeHeaders(
                    array_unique(array_merge(
                        $groupValue,
                        $cors->getExposeHeaders() ?? []
                    ))
                );
            }

            $cors->allowCredentials(null
                ?? $cors->getAllowCredentials()
                ?? $this->cors->getAllowCredentials()
            );

            $cors->maxAge(null
                ?? $cors->getMaxAge()
                ?? $this->cors->getMaxAge()
            );

            $routeBlueprint->cors($cors);
        }

        return $this;
    }
}
