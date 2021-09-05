<?php


namespace Gzhegow\Router\Domain\Loader;

use Gzhegow\Router\Utils;
use Gzhegow\Router\Domain\Route\BlueprintRouteGroup;


/**
 * FilePhpRouteLoader
 */
class FilePhpRouteLoader implements RouteLoaderInterface
{
    /**
     * @param BlueprintRouteGroup $group
     * @param string              $source
     *
     * @return void
     */
    public function loadSource(BlueprintRouteGroup $group, $source) : void
    {
        require $realpath = Utils::thePathFilePhpVal($source);
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return Utils::filterFilePhp($source);
    }
}
