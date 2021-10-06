<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * CallableRouteLoader
 */
class CallableRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouterContainerInterface
     */
    protected $routerContainer;


    /**
     * Constructor
     *
     * @param RouterContainerInterface $routerContainer
     */
    public function __construct(RouterContainerInterface $routerContainer)
    {
        $this->routerContainer = $routerContainer;
    }


    /**
     * @param mixed                $source
     * @param null|RouteCollection $collection
     *
     * @return RouteCollection
     */
    public function loadSource($source, RouteCollection $collection = null) : RouteCollection
    {
        $collection = $collection ?? new RouteCollection();

        /** @todo */
        // call_user_func($source, $collection, $this->routerContainer->getRouteLoader());
        call_user_func($source, $collection);

        return $collection;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return is_callable($source);
    }
}
