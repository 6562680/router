<?php


namespace Gzhegow\Router\Domain\Loader;

use Gzhegow\Router\Domain\Route\BlueprintRouteGroup;


/**
 * DirectoryRouteLoader
 */
class DirectoryRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouteLoaderInterface
     */
    protected $fileRouteLoader;


    /**
     * Constructor
     *
     * @param RouteLoaderInterface $fileRouteLoader
     */
    public function __construct(RouteLoaderInterface $fileRouteLoader)
    {
        $this->fileRouteLoader = $fileRouteLoader;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return false
            || ( $source instanceof \SplFileInfo && $source->isDir() )
            || is_dir($source);
    }


    /**
     * @param BlueprintRouteGroup $group
     * @param string              $source
     *
     * @return void
     */
    public function loadSource(BlueprintRouteGroup $group, $source) : void
    {
        $it = new \RecursiveDirectoryIterator($source);
        $iit = new \RecursiveIteratorIterator($it);

        foreach ( $iit as $spl ) {
            if ($this->fileRouteLoader->supportsSource($spl)) {
                $this->fileRouteLoader->loadSource($group, $spl);
            }
        }
    }
}
