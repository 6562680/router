<?php

namespace Gzhegow\Router\Domain\Endpoint;

use GetOptionKit\OptionCollection;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * Signature
 */
class Signature
{
    /**
     * @var string
     */
    protected $value;
    /**
     * @var string[]
     */
    protected $options = [];


    /**
     * Constructor
     *
     * @param string $signature
     */
    public function __construct(string $signature)
    {
        if (! strlen($signature)) {
            throw new InvalidArgumentException(
                [ 'Invalid signature: %s', $signature ]
            );
        }

        $this->value = $signature;
    }


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return array_filter([
            'value'   => $this->value,
            'options' => $this->options,
        ], function ($v) {
            return ! is_null($v);
        });
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data) : void
    {
        $this->value = $data[ 'value' ] ?? null;
        $this->options = $data[ 'options' ] ?? [];
    }

    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * Constructor
     *
     * @param string[] $options
     */
    public function compile(array $options)
    {
        $value = [];

        try {
            $collection = new OptionCollection();

            foreach ( $options as $definition => $val ) {
                if (is_int($definition)) {
                    $definition = $val;
                    $description = null;

                } else {
                    $description = $val;
                }

                $collection->add($definition, $description);

                $value[ $definition ] = $description;
            }
        }
        catch ( \Throwable $e ) {
            throw new InvalidArgumentException(
                [ 'Invalid signature: %s', $this->value ]
            );
        }

        $this->options = $value;
    }
}
