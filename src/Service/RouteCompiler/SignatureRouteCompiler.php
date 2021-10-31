<?php


namespace Gzhegow\Router\Service\RouteCompiler;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * SignatureRouteCompiler
 */
class SignatureRouteCompiler implements RouteCompilerInterface
{
    /**
     * @param Route $route
     *
     * @return void
     */
    public function compileRoute($route) : void
    {
        $signature = $route->getSignature();
        $signatureValue = $signature->getValue();

        $matches = Helper::strMatch('{', '}', $signatureValue);

        $replacements = [];
        foreach ( $matches as $item ) {
            $search = '{' . $item . '}';
            $replacement = "\0";

            $replacements[ $search ] = $replacement;
        }

        $signatureArray = strtr($signatureValue, $replacements);
        $signatureArray = explode(' ', $signatureArray);

        foreach ( $signatureArray as $word ) {
            if ("\0" !== $word) {
                throw new InvalidArgumentException(
                    [ 'Each signature element should be wrapped into curly braces: %s', $signatureValue ]
                );
            }
        }

        $options = [];
        foreach ( $matches as $match ) {
            [ $definition, $description ] = explode('>', $match, 2) + [ '', null ];

            $definition = ltrim(trim($definition), '-');
            $description = isset($description)
                ? trim($description)
                : null;

            $options[ $definition ] = $description;
        }

        $signature->compile($options);
    }


    /**
     * @param Route $route
     *
     * @return bool
     */
    public function supportsRoute($route) : bool
    {
        if (! ( $route instanceof Route )) {
            return false;
        }

        if (null === $route->hasSignature()) {
            return false;
        }

        return true;
    }
}
