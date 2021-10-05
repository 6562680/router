<?php


namespace Gzhegow\Router\Domain\Route\Blueprint;


/**
 * RouteBlueprint
 */
class RouteBuilder2 extends AbstractRouteGroup
{
    /**
     * @var string
     */
    protected $method;
    /**
     * @var mixed
     */
    protected $action;


    /**
     * @return static
     */
    public function reset()
    {
        parent::reset();

        $this->method = null;
        $this->action = null;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getMethod() : ?string
    {
        return $this->method;
    }

    /**
     * @return null|mixed
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function get($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function post($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function put($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function patch($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function delete($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function purge($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function head($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function options($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function trace($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }

    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function connect($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function cli($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }


    /**
     * @param mixed      $endpoint
     * @param mixed      $action
     * @param null|mixed $name
     *
     * @return static
     */
    public function sock($endpoint, $action, $name = null) : RouteBlueprint
    {
        $this->endpointRegex($endpoint);
        $this->action($action);
        $this->name($name);

        return $this;
    }


    /**
     * @param null|string $method
     *
     * @return static
     */
    public function method(?string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param null|mixed $action
     *
     * @return static
     */
    public function action($action)
    {
        $this->action = $action;

        return $this;
    }
}
