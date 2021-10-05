<?php


namespace Gzhegow\Router\Vendor;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * Helper
 */
class Helper
{
    /**
     * @param string|\SplFileInfo $pathOrSpl
     *
     * @return null|string
     */
    public static function pathFilePhpVal($pathOrSpl) : ?string
    {
        if ($spl = static::filterSplFilePhp($pathOrSpl)) {
            return $spl->getRealPath();
        }

        if ($path = static::filterPathFilePhp($pathOrSpl)) {
            return realpath($path);
        }

        return null;
    }

    /**
     * @param string|\SplFileInfo $pathOrSpl
     *
     * @return null|string
     */
    public static function thePathFilePhpVal($pathOrSpl) : ?string
    {
        if (null === ( $path = static::pathFilePhpVal($pathOrSpl) )) {
            throw new InvalidArgumentException(
                [ 'Invalid file or spl: %s', $pathOrSpl ]
            );
        }

        return $path;
    }


    /**
     * @param array|callable $callableArray
     *
     * @return null|array
     */
    public static function filterCallableArrayPublic($callableArray) : ?array
    {
        if (is_array($callableArray)
            && isset($callableArray[ 0 ]) && is_object($callableArray[ 0 ])
            && isset($callableArray[ 1 ]) && is_string($callableArray[ 1 ]) && ( '' !== $callableArray[ 1 ] )
            && is_callable($callableArray)
        ) {
            return $callableArray;
        }

        return null;
    }

    /**
     * @param array|callable $callableArray
     *
     * @return null|array
     */
    public static function filterCallableArrayStatic($callableArray) : ?array
    {
        if (is_array($callableArray)
            && isset($callableArray[ 1 ]) && is_string($callableArray[ 1 ]) && ( '' !== $callableArray[ 1 ] )
            && isset($callableArray[ 0 ]) && class_exists($callableArray[ 0 ])
            && is_callable($callableArray)
        ) {
            return $callableArray;
        }

        return null;
    }


    /**
     * @param array|callable $callableString
     *
     * @return null|string
     */
    public static function filterCallableStringSemicolon($callableString) : ?string
    {
        if (! is_string($callableString)) {
            return null;
        }

        $classMethod = explode('::', $callableString);
        if (count($classMethod) !== 2) {
            return null;
        }

        try {
            $rm = new \ReflectionMethod(...$classMethod);

            if (! $rm->isPublic()
                || ! $rm->isStatic()
                || $rm->isAbstract()
            ) {
                return null;
            }

            return $callableString;
        }
        catch ( \ReflectionException $e ) {
        }

        return null;
    }


    /**
     * @param string|\SplFileInfo $pathOrSpl
     *
     * @return null|string
     */
    public static function filterDirectory($pathOrSpl) // : ?string|\SplFileInfo
    {
        return null
            ?? static::filterSplDirectory($pathOrSpl)
            ?? static::filterPathDirectory($pathOrSpl);
    }

    /**
     * @param string $filepath
     *
     * @return null|string
     */
    public static function filterPathDirectory($filepath) : ?string
    {
        if (! ( is_string($filepath)
            && is_dir($filepath)
        )) {
            return null;
        }

        return $filepath;
    }

    /**
     * @param \SplFileInfo $spl
     *
     * @return null|\SplFileInfo
     */
    public static function filterSplDirectory($spl) : ?\SplFileInfo
    {
        if (! ( ( $spl instanceof \SplFileInfo )
            && $spl->isDir()
        )) {
            return null;
        }

        return $spl;
    }


    /**
     * @param string|\SplFileInfo $pathOrSpl
     *
     * @return null|string
     */
    public static function filterFilePhp($pathOrSpl) // : ?string|\SplFileInfo
    {
        return null
            ?? static::filterSplFilePhp($pathOrSpl)
            ?? static::filterPathFilePhp($pathOrSpl);
    }

    /**
     * @param string $filepath
     *
     * @return null|string
     */
    public static function filterPathFilePhp($filepath) : ?string
    {
        if (! ( is_string($filepath)
            && is_file($filepath)
            && ( 'php' === pathinfo($filepath, PATHINFO_EXTENSION) )
        )) {
            return null;
        }

        return $filepath;
    }

    /**
     * @param \SplFileInfo $spl
     *
     * @return null|\SplFileInfo
     */
    public static function filterSplFilePhp($spl) : ?\SplFileInfo
    {
        if (! ( ( $spl instanceof \SplFileInfo )
            && $spl->isFile()
            && 'php' === $spl->getExtension()
        )) {
            return null;
        }

        return $spl;
    }


    /**
     * @param string|mixed $patternRegex
     *
     * @return null|string
     */
    public static function filterRegexShort($patternRegex) : ?string
    {
        if (! is_string($patternRegex)) {
            return null;
        }

        if (false === @preg_match('/' . $patternRegex . '/', null)) {
            return null;
        }

        return $patternRegex;
    }


    /**
     * @param string|\SplFileInfo $pathOrSpl
     *
     * @return array
     */
    public static function extractClassesFromFile($pathOrSpl) : array
    {
        static $_cache;

        $_cache = $_cache ?? [];

        $filepath = Helper::thePathFilePhpVal($pathOrSpl);

        if (! isset($_cache[ $filepath ])) {
            $code = [];
            $h = fopen($filepath, 'r');
            while ( ! feof($h) ) {
                $line = trim(fgets($h));
                if (! $line) {
                    continue;
                }

                if (false
                    || ( 0 === mb_stripos($line, $needle = 'class ') )
                    || ( 0 === mb_stripos($line, $needle = 'interface ') )
                    || ( 0 === mb_stripos($line, $needle = 'trait ') )
                    || ( false !== mb_stripos($line, $needle = ' class ') )
                    || ( false !== mb_stripos($line, $needle = ' interface ') )
                    || ( false !== mb_stripos($line, $needle = ' trait ') )
                ) {
                    break;
                }

                $code[] = $line;
            }
            fclose($h);

            $tokens = token_get_all(implode("\n", $code));
            unset($code);

            $classes = [];
            $namespace = null;
            foreach ( $tokens as $token ) {
                if (T_NAMESPACE === $token[ 0 ]) {
                    $namespace = $token[ 1 ];

                } elseif (T_CLASS === $token[ 0 ]) {
                    $classes[] = implode('\\', array_filter([
                        $namespace,
                        $token[ 1 ],
                    ]));
                }
            }

            $_cache[ $filepath ] = $classes;
        }

        return $_cache[ $filepath ];
    }
}
