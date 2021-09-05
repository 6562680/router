<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Exceptions\Runtime\UnexpectedValueException;


/**
 * BlueprintRoute
 */
class BlueprintRoute extends BlueprintNode
{
    /**
     * @var string
     */
    protected $method = 'CLI';
    /**
     * @var string|callable|HandlerInterface|mixed
     */
    protected $action;


    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return string|callable|HandlerInterface|mixed
     */
    public function getAction()
    {
        if (null === $this->action) {
            throw new UnexpectedValueException('Action should be not null');
        }

        return $this->action;
    }


    /**
     * @param string $method
     *
     * @return static
     */
    public function setMethod(string $method)
    {
        $methodUpper = strtoupper(trim($method));

        if (null === $this->configuration->filterMethod($methodUpper)) {
            throw new InvalidArgumentException(
                [ 'Invalid Method: %s', $method ]
            );
        }

        $this->method = $methodUpper;

        return $this;
    }

    /**
     * @param string|callable|HandlerInterface|mixed $action
     *
     * @return static
     */
    public function setAction($action)
    {
        if (null === $this->configuration->filterAction($action)) {
            throw new InvalidArgumentException(
                [ 'Invalid Action: %s', $action ]
            );
        }

        $this->action = $action;

        return $this;
    }


    /**
     * @param string $method
     *
     * @return static
     */
    public function method(string $method)
    {
        $this->setMethod($method);

        return $this;
    }

    /**
     * @param string|callable|object|mixed $action
     *
     * @return static
     */
    public function action($action)
    {
        $this->setAction($action);

        return $this;
    }
}
