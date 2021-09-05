<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * Utils
 */
class Utils
{
    /**
     * @param string    $haystack
     * @param string    $needle
     * @param null|bool $ignoreCase
     *
     * @return null|string
     */
    public static function strStarts(string $haystack, string $needle, bool $ignoreCase = null) : ?string
    {
        $ignoreCase = $ignoreCase ?? true;

        if ('' === $haystack) return null;
        if ('' === $needle) return $haystack;

        $pos = $ignoreCase
            ? mb_stripos($haystack, $needle)
            : mb_strpos($haystack, $needle);

        $result = 0 === $pos
            ? mb_substr($haystack, mb_strlen($needle))
            : null;

        return $result;
    }


    /**
     * @param string|\SplFileInfo $pathOrSpl
     *
     * @return array
     */
    public static function getClassesFromFile($pathOrSpl) : array
    {
        static $_cache;

        $_cache = $_cache ?? [];

        $filepath = static::thePathFilePhpVal($pathOrSpl);

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

                    continue;
                }

                if (T_CLASS === $token[ 0 ]) {
                    $classes[] = implode('\\', array_filter([
                        $namespace,
                        $token[ 1 ],
                    ]));

                    continue;
                }
            }

            $_cache[ $filepath ] = $classes;
        }

        return $_cache[ $filepath ];
    }

    /**
     * @param \Closure $closure
     *
     * @return null|\Closure
     */
    public static function filterClosure($closure) : ?\Closure
    {
        if ($closure instanceof \Closure) {
            return $closure;
        }

        return null;
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
        if (! ( $spl instanceof \SplFileInfo )
            && $spl->isFile()
            && 'php' === $spl->getExtension()
        ) {
            return null;
        }

        return $spl;
    }

    /**
     * @param string $namespace
     *
     * @return null|string
     */
    public static function filterClassFullname($namespace) : ?string
    {
        if (! is_string($namespace)) {
            return null;
        }

        if ('' === $namespace) {
            return null;
        }

        $phpClass = ltrim($namespace, '\\');

        $firstLetter = substr($phpClass, 0, 1);
        if (ctype_digit($firstLetter)) {
            return null;
        }

        $test = preg_replace('~[a-z0-9_\x80-\xff]*~iu', '', $namespace);

        $letters = '' === $test
            ? [] : str_split($test);

        foreach ( $letters as $letter ) {
            if ($letter !== '\\') {
                return null;
            }
        }

        return $namespace;
    }

    /**
     * @param string $regex
     *
     * @return null|string
     */
    public static function filterRegexShort($regex) : ?string
    {
        if (! is_string($regex)) {
            return null;
        }

        if (false === @preg_match('/' . $regex . '/', null)) {
            return null;
        }

        return $regex;
    }

    /**
     * @param string $value
     *
     * @return null|string
     */
    public static function filterStrUtf8($value) : ?string
    {
        if (! is_string($value)) {
            return null;

        } elseif ('' === $value) {
            return null;
        }

        for ( $i = 0; $i < strlen($value); $i++ ) {
            if (ord($value[ $i ]) < 0x80) {
                continue; // 0bbbbbbb
            }

            if (( ord($value[ $i ]) & 0xE0 ) === 0xC0) {
                $n = 1; // 110bbbbb
            } elseif (( ord($value[ $i ]) & 0xF0 ) === 0xE0) {
                $n = 2; // 1110bbbb
            } elseif (( ord($value[ $i ]) & 0xF8 ) === 0xF0) {
                $n = 3; // 11110bbb
            } elseif (( ord($value[ $i ]) & 0xFC ) === 0xF8) {
                $n = 4; // 111110bb
            } elseif (( ord($value[ $i ]) & 0xFE ) === 0xFC) {
                $n = 5; // 1111110b
            } else {
                return null; // Does not match any model
            }

            for ( $j = 0; $j < $n; $j++ ) { // n bytes matching 10bbbbbb follow ?
                if (++$i === strlen($value) || ( ( ord($value[ $i ]) & 0xC0 ) !== 0x80 )) {
                    return null;
                }
            }
        }

        return $value;
    }

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
}
