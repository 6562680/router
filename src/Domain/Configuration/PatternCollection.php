<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * PatternCollection
 */
class PatternCollection
{
    /**
     * @var array
     */
    protected $patterns = [
        '*'          => '.+',
        'id'         => '[0-9]+',
        'controller' => '(?:[^\/]+\/)+(?=[^\/]+)',
        'action'     => '[^\/]+(?=|$)',
    ];


    /**
     * @return array
     */
    public function getPatterns() : array
    {
        return $this->patterns;
    }


    /**
     * @param string|string[] $patterns
     *
     * @return static
     */
    public function setPatterns($patterns)
    {
        $this->patterns = [];

        $patterns = is_iterable($patterns)
            ? $patterns
            : [ $patterns ];

        $this->addPatterns($patterns);

        return $this;
    }

    /**
     * @param string|string[] $patterns
     *
     * @return static
     */
    public function addPatterns($patterns)
    {
        $patterns = is_iterable($patterns)
            ? $patterns
            : [ $patterns ];

        foreach ( $patterns as $patternName => $patternRegex ) {
            $this->addPattern($patternName, $patternRegex);
        }

        return $this;
    }

    /**
     * @param string $patternName
     * @param string $patternRegex
     *
     * @return static
     */
    public function addPattern(string $patternName, string $patternRegex)
    {
        if (null === $this->filterPatternName($patternName)) {
            throw new InvalidArgumentException(
                [ 'Invalid PatternName: %s', $patternName ]
            );
        }

        if (null === $this->filterPatternRegex($patternRegex)) {
            throw new InvalidArgumentException(
                [ 'Invalid PatternRegex: %s', $patternRegex ]
            );
        }

        $this->patterns[ $patternName ] = $patternRegex;

        return $this;
    }


    /**
     * @param string|mixed $patternName
     *
     * @return null|string
     */
    public function filterPatternName($patternName) : ?string
    {
        if (false === preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $patternName)) {
            return null;
        }

        return $patternName;
    }

    /**
     * @param string|mixed $patternRegex
     *
     * @return null|string
     */
    public function filterPatternRegex($patternRegex) : ?string
    {
        return Helper::filterRegexShort($patternRegex);
    }
}
