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
    protected $blueprints = [];

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
    public function setBlueprints($routes) : BlueprintGroup
    {
        if (null === $routes) {
            $this->blueprints = [];

        } else {
            $this->addBlueprints($routes);
        }

        return $this;
    }

    /**
     * @param null|Blueprint|iterable $routes
     *
     * @return static
     */
    public function addBlueprints($routes)
    {
        $routes = is_iterable($routes)
            ? $routes
            : [ $routes ];

        foreach ( $routes as $route ) {
            $this->addBlueprint($route);
        }

        return $this;
    }

    /**
     * @param Blueprint $route
     *
     * @return static
     */
    public function addBlueprint(Blueprint $route)
    {
        $this->blueprints[] = $route;

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
     * @param string     $method
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function route(string $method, $endpoint, $action, $name = null) : Blueprint
    {
        $this->blueprints[] = $blueprint = new Blueprint();

        $blueprint->method($method);
        $blueprint->endpoint($endpoint);
        $blueprint->action($action);
        $blueprint->name($name);

        return $blueprint;
    }


    /**
     * @return Blueprint[]
     */
    public function flushBlueprints() : array
    {
        $blueprints = $this->blueprints;
        $this->blueprints = [];

        foreach ( $blueprints as $blueprint ) {
            $this->applyTo($blueprint);
        }

        return $blueprints;
    }


    /**
     * @param Blueprint $blueprint
     *
     * @return static
     */
    protected function applyTo(Blueprint $blueprint)
    {
        $action = $blueprint->getAction();

        if (( null !== $this->namespace )
            && is_string($action)
            && ! is_callable($action)
        ) {
            $blueprint->action($this->namespace
                . '\\' . ltrim($action, '\\')
            );
        }

        if (null !== $this->endpoint) {
            $blueprint->endpoint($this->endpoint
                . '/' . ltrim($blueprint->getEndpoint(), '/')
            );
        }

        if (null !== $this->signature) {
            $blueprint->signature($this->signature
                . ' ' . ltrim($blueprint->getSignature(), ' ')
            );
        }

        if (null !== $this->name) {
            $blueprint->name($this->name
                . $blueprint->getName()
            );
        }

        if (null !== $this->description) {
            $blueprint->description($this->description
                . $blueprint->getDescription()
            );
        }

        if ($this->bindings) {
            $blueprint->bindings([]
                + $blueprint->getBindings() // 1
                + $this->bindings // 2
            );
        }

        if ($this->middlewares) {
            $blueprint->middlewares(
                array_unique(array_merge([],
                    $this->middlewares, // 1
                    $blueprint->getMiddlewares(), // 2
                ))
            );
        }

        if ($this->tags) {
            $blueprint->tags(
                array_unique(array_merge([],
                    $this->tags, // 1
                    $blueprint->getTags(), // 2
                ))
            );
        }

        $this->applyCorsTo($blueprint);

        return $this;
    }

    /**
     * @param Blueprint $blueprint
     *
     * @return static
     */
    protected function applyCorsTo(Blueprint $blueprint)
    {
        if (null !== $this->cors) {
            $cors = null
                ?? $blueprint->getCors()
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

            $blueprint->cors($cors);
        }

        return $this;
    }
}
