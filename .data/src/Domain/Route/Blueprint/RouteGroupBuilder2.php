<?php


namespace Gzhegow\Router\Domain\Route\Blueprint;


/**
 * RouteGroupBuilder
 */
class RouteGroupBuilder2 extends AbstractRouteGroup
{
    /**
     * @var RouteBlueprint[]
     */
    protected $routes = [];
    /**
     * @var RouteGroupBuilder[]
     */
    protected $groups = [];

    /**
     * @var mixed
     */
    protected $namespace;
    /**
     * @var mixed
     */
    protected $source;


    /**
     * @return static
     */
    public function reset()
    {
        parent::reset();

        $this->routes = [];
        $this->groups = [];

        $this->namespace = null;
        $this->source = null;

        return $this;
    }


    /**
     * @return RouteBlueprint[]
     */
    public function getRoutes() : array
    {
        if ($this->groups) {
            $groups = $this->groups;
            $this->groups = [];

            foreach ( $groups as $group ) {
                // ! recursion
                foreach ( $group->getRoutes() as $blueprint ) {
                    $routeAction = $action = $blueprint->getAction();
                    if (! is_callable($routeAction) && is_string($routeAction)) {
                        $action = [];
                        $action[] = $this->getNamespace();
                        $action[] = $routeAction;
                        $action = implode('\\', array_filter($action));
                    }

                    $bindings = []
                        + $group->getBindings()
                        + $blueprint->getBindings();

                    $middlewares = []
                        + $group->getMiddlewares()
                        + $blueprint->getMiddlewares();

                    $tags = []
                        + $group->getTags()
                        + $blueprint->getTags();

                    $blueprint->endpoint($this->getEndpoint() . $blueprint->getEndpoint());
                    $blueprint->endpointRegex($this->getEndpointRegex() . $blueprint->getEndpointRegex());

                    $blueprint->name($this->getName() . $blueprint->getName());
                    $blueprint->description($this->getDescription() . $blueprint->getDescription());

                    $blueprint->action($action);

                    $blueprint->bindings($bindings);
                    $blueprint->middlewares($middlewares);
                    $blueprint->tags($tags);

                    $this->routes[] = $blueprint;
                }
            }
        }

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
     * @return null|mixed
     */
    public function getSource()
    {
        return $this->source;
    }


    /**
     * @param null|string $namespace
     *
     * @return static
     */
    public function namespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param null|mixed $source
     *
     * @return static
     */
    public function source($source)
    {
        $this->source = $source;

        return $this;
    }


    /**
     * @param RouteBlueprint $route
     *
     * @return static
     */
    public function route(RouteBlueprint $route)
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @param RouteGroupBuilder $group
     *
     * @return static
     */
    public function group(RouteGroupBuilder $group)
    {
        $this->groups[] = $group;

        return $this;
    }
}
