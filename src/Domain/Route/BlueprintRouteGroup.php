<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Configuration\Configuration;


/**
 * BlueprintRouteGroup
 */
class BlueprintRouteGroup extends BlueprintNode
{
    /**
     * @var BlueprintRouteCollection
     */
    protected $routeBlueprintCollection;

    /**
     * @var static
     */
    protected $parent;


    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->routeBlueprintCollection = new BlueprintRouteCollection();

        parent::__construct($configuration);
    }


    /**
     * @param null|static $routeGroup
     *
     * @return static
     */
    public function withParent(?self $routeGroup)
    {
        $this->parent = $routeGroup;

        return $this;
    }


    /**
     * @return null|static
     */
    public function getParent() : ?self
    {
        return $this->parent;
    }


    /**
     * @param BlueprintRoute $blueprint
     *
     * @return static
     */
    public function addBlueprint(BlueprintRoute $blueprint)
    {
        $this->routeBlueprintCollection->addBlueprint($blueprint);

        return $this;
    }


    /**
     * @param BlueprintRouteGroup $routeGroup
     *
     * @return static
     */
    public function mount(BlueprintRouteGroup $routeGroup)
    {
        $routeBlueprints = $this->flush();

        foreach ( $routeBlueprints as $routeBlueprint ) {
            $routeGroup->addBlueprint($routeBlueprint);
        }

        return $this;
    }

    /**
     * @return BlueprintRoute[]
     */
    public function flush() : array
    {
        $endpointSeparator = $this->configuration->getSeparatorCollection()->getSeparatorPrimary();
        $namespaceSeparator = '\\';
        $nameSeparator = '.';

        $routeBlueprints = $this->routeBlueprintCollection->flushBlueprints();

        foreach ( $routeBlueprints as $routeBlueprint ) {
            $endpoint = [];
            $endpoint[] = $this->getEndpoint();
            $endpoint[] = $routeBlueprint->getEndpoint();
            $endpoint = implode($endpointSeparator, array_filter($endpoint));

            $namespace = [];
            $namespace[] = $this->getNamespace();
            $namespace[] = $routeBlueprint->getNamespace();
            $namespace = implode($namespaceSeparator, array_filter($namespace)) ?: null;

            $name = [];
            $name[] = $this->getName();
            $name[] = $routeBlueprint->getName();
            $name = implode($nameSeparator, array_filter($name)) ?: null;

            $bindings = []
                + $routeBlueprint->getBindings()
                + $this->getBindings();

            $tags = []
                + $routeBlueprint->getTags()
                + $this->getTags();

            $middlewares = []
                + $routeBlueprint->getMiddlewares()
                + $this->getMiddlewares();

            $action = $routeBlueprint->getAction();

            // @todo
            // if (null === Utils::filterCallableArrayPublic($action)) {
            //     if (null !== $this->configuration->filterActionAsterisk($action)) {
            //         [ $controller, $method ] = explode('@', $action);
            //
            //         $action = [ $this->getNamespace() . $controller, $method ];
            //
            //     } elseif (is_array($action)) {
            //         [ $controller, $method ] = $action;
            //
            //         $action = [ $this->getNamespace() . $controller, $method ];
            //     }
            // }

            $routeBlueprint->endpoint($endpoint);
            $routeBlueprint->namespace($namespace);
            $routeBlueprint->action($action);
            $routeBlueprint->name($name);

            $routeBlueprint->bindings($bindings);
            $routeBlueprint->tags($tags);

            $routeBlueprint->middlewares($middlewares);
        }

        return $routeBlueprints;
    }
}
