<?php


namespace Gzhegow\Router\Domain\Route\Specification;

use GetOptionKit\OptionParser;
use GetOptionKit\OptionCollection;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Exceptions\Runtime\BadMethodCallException;
use Gzhegow\Router\Exceptions\Exception\InvalidSignatureException;


/**
 * CliRouteSpecification
 */
class CliRouteSpecification implements RouteSpecificationInterface
{
    /**
     * @var string
     */
    protected $command;


    /**
     * @return OptionCollection
     */
    protected function newOptionCollection() : OptionCollection
    {
        return new OptionCollection();
    }

    /**
     * @param OptionCollection $optionCollection
     *
     * @return OptionParser
     */
    protected function newOptionParser(OptionCollection $optionCollection) : OptionParser
    {
        return new OptionParser($optionCollection);
    }


    /**
     * @return string
     */
    public function getCommand() : string
    {
        return $this->command;
    }


    /**
     * @param RouteCollection $routeCollection
     *
     * @return array
     */
    public function matches(RouteCollection $routeCollection) : array
    {
        if (null === $this->command) {
            throw new BadMethodCallException(
                [ 'Specification requires defined `command`: %s', $this ]
            );
        }

        $routes = [];

        $rows = $routeCollection->getRoutes();
        $index = $routeCollection->getRoutesIndexByName('method');

        foreach ( $index[ 'CLI' ] ?? [] as $id ) {
            $routes[] = $rows[ $id ];
        }

        return $routes;
    }

    /**
     * @param Route $route
     *
     * @return null|Route
     * @throws InvalidSignatureException
     */
    public function match(Route $route) : ?Route
    {
        $endpoint = $route->getEndpoint();
        $endpointPath = $endpoint->getValue();
        $endpointRegex = $endpoint->getRegex();

        $arguments = explode(' ', $this->command);
        $arguments = array_filter($arguments, 'strlen');

        $endpoint = array_shift($arguments);

        $matches = [];
        $isEndpointMatch = false
            || ( $endpointRegex && preg_match($endpointRegex, $endpoint, $matches) )
            || ( $endpointPath && $endpointPath === $endpoint );

        if (! $isEndpointMatch) {
            return null;
        }

        // ! deep clone
        $routeMatched = unserialize(serialize($route));

        $bindings = [];

        if ($matches) {
            foreach ( $matches as $idx => $value ) {
                if (is_string($idx) && $idx) {
                    $bindings[ $idx ] = $value;
                }
            }
        }

        if ($arguments) {
            $optionCollection = new OptionCollection();
            $optionParser = new OptionParser($optionCollection);

            $signature = $route->getSignature();
            $signatureOptions = $signature->getOptions();

            foreach ( $signatureOptions as $definition => $description ) {
                $optionCollection->add($definition, $description);
            }

            try {
                $parsedOptionArray = $optionParser->parse($arguments);
            }
            catch ( \Exception $e ) {
                throw new InvalidSignatureException(
                    [ 'Unable to parse arguments: %s', $this->command ]
                );
            }

            foreach ( $parsedOptionArray as $binding => $option ) {
                $bindings[ $binding ] = $option->value;
            }
        }

        if ($bindings) {
            $routeMatched->addBindings($bindings);
        }

        return $routeMatched;
    }


    /**
     * @param string $command
     *
     * @return static
     */
    public function command(string $command)
    {
        $command = trim($command);

        $this->command = $command;

        return $this;
    }
}
