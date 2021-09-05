<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * SeparatorCollection
 */
class SeparatorCollection
{
    /**
     * @var array
     */
    protected $separators = [ '/' ];


    /**
     * @return array
     */
    public function getSeparators() : array
    {
        return $this->separators;
    }

    /**
     * @return string
     */
    public function getSeparatorPrimary() : string
    {
        return $this->separators[ 0 ];
    }


    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function setSeparators($separators)
    {
        $separators = is_array($separators)
            ? $separators
            : [ $separators ];

        $this->separators = [];

        $this->addSeparators($separators);

        return $this;
    }


    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function addSeparators($separators)
    {
        $separators = is_array($separators)
            ? $separators
            : [ $separators ];

        foreach ( $separators as $separator ) {
            $this->addSeparator($separator);
        }

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return static
     */
    public function addSeparator(string $separator)
    {
        if (null === $this->filterSeparator($separator)) {
            throw new InvalidArgumentException(
                [ 'Invalid Separator: %s', $separator ]
            );
        }

        $uniq = [];
        foreach ( $this->separators as $separator ) {
            $uniq[ $separator ] = true;
        }

        $this->separators = array_keys($uniq);

        return $this;
    }


    /**
     * @param string|mixed $separator
     *
     * @return null|string
     */
    public function filterSeparator($separator) : ?string
    {
        if (! is_string($separator)) {
            return null;
        }

        if (1 !== strlen($separator)) {
            return null;
        }

        return $separator;
    }
}
