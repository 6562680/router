<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Exceptions\Runtime\OverflowException;


/**
 * BlueprintRouteCollection
 */
class BlueprintRouteCollection
{
    /**
     * @var BlueprintRoute[]
     */
    protected $blueprints = [];

    /**
     * @var array
     */
    protected $uniq = [
        'method.endpoint' => [],
        'name'            => [],
    ];


    /**
     * @return BlueprintRoute[]
     */
    public function getBlueprints() : array
    {
        return $this->blueprints;
    }


    /**
     * @param BlueprintRoute|BlueprintRoute[] $blueprints
     *
     * @return static
     */
    public function setBlueprints($blueprints)
    {
        $blueprints = is_array($blueprints)
            ? $blueprints
            : [ $blueprints ];

        $this->blueprints = [];

        $this->addBlueprints($blueprints);

        return $this;
    }


    /**
     * @param BlueprintRoute|BlueprintRoute[] $blueprints
     *
     * @return static
     */
    public function addBlueprints($blueprints)
    {
        $blueprints = is_array($blueprints)
            ? $blueprints
            : [ $blueprints ];

        foreach ( $blueprints as $route ) {
            $this->addBlueprint($route);
        }

        return $this;
    }


    /**
     * @param BlueprintRoute $blueprint
     *
     * @return static
     */
    public function addBlueprint(BlueprintRoute $blueprint)
    {
        $routeMethod = $blueprint->getMethod();
        $routeEndpoint = $blueprint->getEndpoint();
        $routeName = $blueprint->getName();

        $uniqMethodEndpoint = $routeMethod . '.' . $routeEndpoint;
        $uniqName = $routeName;

        if (isset($this->uniq[ 'method.endpoint' ][ $uniqMethodEndpoint ])) {
            throw new OverflowException(
                [ 'Route is already exists: %s', $uniqMethodEndpoint ]
            );
        }

        if (null !== $uniqName) {
            if (isset($this->uniq[ 'name' ][ $uniqName ])) {
                throw new OverflowException(
                    [ 'Route name should be unique: %s', $uniqName ]
                );
            }
        }

        $this->blueprints[] = $blueprint;

        end($this->blueprints);
        $idx = key($this->blueprints);

        $this->uniq[ 'method.endpoint' ][ $uniqMethodEndpoint ] = $idx;

        if (isset($uniqName)) {
            $this->uniq[ 'name' ][ $uniqName ] = $idx;
        }

        return $this;
    }


    /**
     * @return BlueprintRoute[]
     */
    public function flushBlueprints() : array
    {
        $blueprints = $this->blueprints;

        $this->blueprints = [];
        $this->uniq = static::$uniqDefault;

        return $blueprints;
    }


    /**
     * @var array
     */
    protected static $uniqDefault = [
        'method.endpoint' => [],
        'name'            => [],
    ];
}
