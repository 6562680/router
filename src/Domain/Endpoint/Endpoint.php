<?php

namespace Gzhegow\Router\Domain\Endpoint;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


class Endpoint
{
    /**
     * @var string
     */
    protected $value;
    /**
     * @var null|string
     */
    protected $regex;


    /**
     * Constructor
     *
     * @param string $endpoint
     */
    public function __construct(string $endpoint)
    {
        if (! strlen($endpoint)) {
            throw new InvalidArgumentException(
                [ 'Invalid endpoint: %s', $endpoint ]
            );
        }

        $this->value = $endpoint;
    }


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return array_filter([
            'endpoint' => $this->value,
            'regex'    => $this->regex,
        ], function ($v) {
            return ! is_null($v);
        });
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data) : void
    {
        $this->value = $data[ 'endpoint' ] ?? null;
        $this->regex = $data[ 'regex' ] ?? null;
    }


    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @return null|string
     */
    public function getRegex() : ?string
    {
        return $this->regex;
    }


    /**
     * @param null|string $regex
     *
     * @return Endpoint
     */
    public function compile(?string $regex)
    {
        if (null !== $regex) {
            if (! strlen($regex)) {
                throw new InvalidArgumentException(
                    [ 'Regex should be not empty: %s', $regex ]
                );
            }

            if (false === @preg_match($regex, '')) {
                throw new InvalidArgumentException(
                    [ 'Invalid regex: %s', $regex ]
                );
            }
        }

        $this->regex = $regex;

        return $this;
    }
}
