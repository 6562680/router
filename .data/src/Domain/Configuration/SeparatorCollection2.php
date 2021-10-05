<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * SeparatorCollection
 */
class SeparatorCollection2
{
    /**
     * @var array
     */
    protected $endpointSeparators = [ '/' ];
    /**
     * @var array
     */
    protected $nameSeparators = [ '.' ];
    /**
     * @var array
     */
    protected $descriptionSeparators = [ "\n" ];


    /**
     * @return array
     */
    public function getEndpointSeparators() : array
    {
        return $this->endpointSeparators;
    }

    /**
     * @param null|int $offset
     *
     * @return string
     */
    public function getEndpointSeparator(int $offset = null) : string
    {
        $offset = $offset ?? 0;

        return $this->endpointSeparators[ $offset ];
    }


    /**
     * @return array
     */
    public function getNameSeparators() : array
    {
        return $this->nameSeparators;
    }

    /**
     * @param null|int $offset
     *
     * @return string
     */
    public function getNameSeparator(int $offset = null) : string
    {
        $offset = $offset ?? 0;

        return $this->nameSeparators[ $offset ];
    }


    /**
     * @return array
     */
    public function getDescriptionSeparators() : array
    {
        return $this->nameSeparators;
    }

    /**
     * @param null|int $offset
     *
     * @return string
     */
    public function getDescriptionSeparator(int $offset = null) : string
    {
        $offset = $offset ?? 0;

        return $this->nameSeparators[ $offset ];
    }


    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function setEndpointSeparators($separators)
    {
        $this->endpointSeparators = [];

        $this->addEndpointSeparators($separators);

        return $this;
    }

    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function addEndpointSeparators($separators)
    {
        $separators = is_iterable($separators)
            ? $separators
            : [ $separators ];

        foreach ( $separators as $separator ) {
            $this->addEndpointSeparator($separator);
        }

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return static
     */
    public function addEndpointSeparator(string $separator)
    {
        if (null === $this->filterSeparator($separator)) {
            throw new InvalidArgumentException(
                [ 'Invalid Separator: %s', $separator ]
            );
        }

        $uniq = [];
        foreach ( $this->endpointSeparators as $separator ) {
            $uniq[ $separator ] = true;
        }

        $this->endpointSeparators = array_keys($uniq);

        return $this;
    }


    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function setNameSeparators($separators)
    {
        $this->nameSeparators = [];

        $this->addNameSeparators($separators);

        return $this;
    }

    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function addNameSeparators($separators)
    {
        $separators = is_iterable($separators)
            ? $separators
            : [ $separators ];

        foreach ( $separators as $separator ) {
            $this->addNameSeparator($separator);
        }

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return static
     */
    public function addNameSeparator(string $separator)
    {
        if (null === $this->filterSeparator($separator)) {
            throw new InvalidArgumentException(
                [ 'Invalid Separator: %s', $separator ]
            );
        }

        $uniq = [];
        foreach ( $this->nameSeparators as $separator ) {
            $uniq[ $separator ] = true;
        }

        $this->nameSeparators = array_keys($uniq);

        return $this;
    }


    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function setDescriptionSeparators($separators)
    {
        $this->descriptionSeparators = [];

        $this->addDescriptionSeparators($separators);

        return $this;
    }

    /**
     * @param string|string[] $separators
     *
     * @return static
     */
    public function addDescriptionSeparators($separators)
    {
        $separators = is_iterable($separators)
            ? $separators
            : [ $separators ];

        foreach ( $separators as $separator ) {
            $this->addDescriptionSeparator($separator);
        }

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return static
     */
    public function addDescriptionSeparator(string $separator)
    {
        if (null === $this->filterSeparator($separator)) {
            throw new InvalidArgumentException(
                [ 'Invalid Separator: %s', $separator ]
            );
        }

        $uniq = [];
        foreach ( $this->descriptionSeparators as $separator ) {
            $uniq[ $separator ] = true;
        }

        $this->descriptionSeparators = array_keys($uniq);

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
