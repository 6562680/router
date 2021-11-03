<?php


namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Exceptions\Runtime\OutOfBoundsException;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * Blueprint
 */
class Blueprint extends AbstractBlueprint
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
     * @return Route
     */
    public function build() : Route
    {
        $separator = '';
        $endpoint = $this->getEndpoint();
        if (preg_match('/[\p{P}\p{S}]/u', substr($endpoint, 0, 1), $m)) {
            $endpoint = str_replace($separator = $m[ 0 ], "\0", $endpoint);
        }

        $endpoint = implode($separator, array_filter(
            explode("\0", $endpoint), 'strlen'
        ));

        $command = rtrim($endpoint . ' ' . $this->getSignature());

        $compiledRoute = new Route(
            $this->getMethod(),
            $command,
            $this->getAction()
        );

        if ($value = $this->getName()) {
            $compiledRoute->setName($value);
        }

        if ($value = $this->getDescription()) {
            $compiledRoute->setDescription($value);
        }

        if ($value = $this->getBindings()) {
            $compiledRoute->setBindings($value);
        }

        if ($value = $this->getMiddlewares()) {
            $compiledRoute->setMiddlewares($value);
        }

        if ($value = $this->getTags()) {
            $compiledRoute->setTags($value);
        }

        return $compiledRoute;
    }


    /**
     * @return string
     */
    public function getMethod() : string
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
     * @param string $method
     *
     * @return static
     */
    public function method(string $method)
    {
        $enum = [
            'CLI' => true,

            'HEAD'    => true,
            'OPTIONS' => true,

            'GET'    => true,
            'POST'   => true,
            'PUT'    => true,
            'PATCH'  => true,
            'DELETE' => true,
            'PURGE'  => true,

            'TRACE'   => true,
            'CONNECT' => true,
        ];

        $value = strtoupper(trim($method));

        if (! isset($enum[ $value ])) {
            throw new OutOfBoundsException(
                [ 'Invalid method: %s', $method ]
            );
        }

        $this->method = $value;

        return $this;
    }

    /**
     * @param mixed $action
     *
     * @return static
     */
    public function action($action)
    {
        if (null === $action) {
            throw new InvalidArgumentException(
                [ 'Invalid action: %s', $action ]
            );
        }

        $this->action = $action;

        return $this;
    }
}
