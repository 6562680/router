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
        $command = [];
        $command[] = $this->getEndpoint();
        $command[] = $this->getSignature();
        $command = implode(' ', array_filter($command, 'strlen'));

        $compiledRoute = new Route($this->getMethod(),
            $command, $this->getAction()
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

            'SOCK' => true,
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
