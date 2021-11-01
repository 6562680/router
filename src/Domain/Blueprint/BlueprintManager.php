<?php


namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Cors\CorsBuilder;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
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
    protected $blueprints = [];


    /**
     * @param RouteLoaderInterface $routeLoader
     * @param null|object          $newthis
     *
     * @return static
     */
    public function load(RouteLoaderInterface $routeLoader, object $newthis = null)
    {
        /** @var BlueprintGroup $group */

        $queue = $this->flushGroups();

        $stack = [];
        while ( null !== key($queue) ) {
            $stack[] = $group = array_shift($queue);

            $source = $group->getSource();

            if (! $routeLoader->supportsSource($source)) {
                throw new UnexpectedValueException(
                    [ 'Invalid source: %s', $source ]
                );
            }

            $routeLoader->loadSource($source, $newthis ?? $this);

            $routes = $this->flushBlueprints();
            $groups = $this->flushGroups();

            $group->addBlueprints($routes);

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
                $parentGroup->addBlueprints($group->flushBlueprints());

            } else {
                foreach ( $group->flushBlueprints() as $route ) {
                    $routes[] = $route;
                }
            }

            prev($stack);
        }

        $this->blueprints = $routes;

        return $this;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return Blueprint
     */
    public function get($endpoint, $action, $name = null) : Blueprint
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
    public function post($endpoint, $action, $name = null) : Blueprint
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
    public function put($endpoint, $action, $name = null) : Blueprint
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
    public function patch($endpoint, $action, $name = null) : Blueprint
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
    public function delete($endpoint, $action, $name = null) : Blueprint
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
    public function purge($endpoint, $action, $name = null) : Blueprint
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
    public function head($endpoint, $action, $name = null) : Blueprint
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
    public function options($endpoint, $action, $name = null) : Blueprint
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
    public function trace($endpoint, $action, $name = null) : Blueprint
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
    public function connect($endpoint, $action, $name = null) : Blueprint
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
    public function cli($endpoint, $action, $name = null) : Blueprint
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
    public function sock($endpoint, $action, $name = null) : Blueprint
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
     * @param null|string $signature
     *
     * @return BlueprintGroup
     */
    public function signature(?string $signature)
    {
        $group = $this->group(null);
        $group->signature($signature);

        return $group;
    }


    /**
     * @param null|string $name
     *
     * @return BlueprintGroup
     */
    public function name(?string $name)
    {
        $group = $this->group(null);
        $group->name($name);

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

        $endpoint = is_iterable($endpoint)
            ? $endpoint
            : [ $endpoint ];

        $endpointPath = [];
        $endpointSignature = [];
        foreach ( $endpoint as $e ) {
            if (! is_string($e)) {
                throw new InvalidArgumentException(
                    [ 'Invalid Endpoint: %s', $endpoint ]
                );
            }

            if ([] === $endpointPath) {
                [ $path, $signature ] = explode(' ', $e, 2) + [ null, null ];

                $path = trim($path) ?: null;
                $signature = trim($signature) ?: null;

            } else {
                $path = null;
                $signature = trim($e) ?: null;
            }

            if ($path) {
                $endpointPath[] = $path;
            }

            if ($signature) {
                $endpointSignature[] = $signature;
            }
        }

        $endpointPath = implode(' ', $endpointPath) ?: null;
        $endpointSignature = implode(' ', $endpointSignature) ?: null;

        $blueprint->method($method);
        $blueprint->action($action);
        $blueprint->name($name);

        $blueprint->endpoint($endpointPath);
        $blueprint->signature($endpointSignature);

        return $blueprint;
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
    public function flushBlueprints() : array
    {
        $routes = $this->blueprints;
        $this->blueprints = [];

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
