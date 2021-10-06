<?php

namespace Gzhegow\Router\Domain\Cors;


use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * CorsBuilder
 */
class CorsBuilder
{
    /**
     * @var null|array
     */
    protected $allowOrigins;

    /**
     * @var null|array
     */
    protected $allowHeaders;
    /**
     * @var null|array
     */
    protected $exposeHeaders;

    /**
     * @var null|bool
     */
    protected $allowCredentials;

    /**
     * @var int
     */
    protected $maxAge;


    /**
     * @param mixed $cors
     *
     * @return static
     */
    public static function from($cors)
    {
        if ($cors instanceof static) {
            $value = $cors;

        } elseif (is_callable($cors)) {
            $value = new static();

            $result = $cors($value);
            if ($result instanceof CorsBuilder) {
                $value = $result;
            }

        } else {
            throw new InvalidArgumentException(
                [ 'Invalid CORS: %s', $cors ]
            );
        }

        return $value;
    }


    /**
     * @return Cors
     */
    public function build() : Cors
    {
        return new Cors(
            $this->allowOrigins,

            $this->allowHeaders,
            $this->exposeHeaders,

            $this->allowCredentials,
            $this->maxAge
        );
    }


    /**
     * @return null|array
     */
    public function getAllowOrigins() : ?array
    {
        return null !== $this->allowOrigins
            ? array_keys($this->allowOrigins)
            : null;
    }


    /**
     * @return null|array
     */
    public function getAllowHeaders() : ?array
    {
        return null !== $this->allowHeaders
            ? array_keys($this->allowHeaders)
            : null;
    }

    /**
     * @return null|array
     */
    public function getExposeHeaders() : ?array
    {
        return null !== $this->exposeHeaders
            ? array_keys($this->exposeHeaders)
            : null;
    }


    /**
     * @return null|bool
     */
    public function getAllowCredentials() : ?bool
    {
        return $this->allowCredentials;
    }


    /**
     * @return null|int
     */
    public function getMaxAge() : ?int
    {
        return $this->maxAge;
    }


    /**
     * @param null|string|iterable $allowOrigins
     *
     * @return static
     */
    public function allowOrigins($allowOrigins)
    {
        if (is_null($allowOrigins)) {
            $this->allowOrigins = null;

        } else {
            $allowOrigins = is_iterable($allowOrigins)
                ? $allowOrigins
                : [ $allowOrigins ];

            foreach ( $allowOrigins as $allowOrigin ) {
                if (! Helper::filterRegexShort($allowOrigin)) {
                    $allowOrigin = preg_quote($allowOrigin, '/');
                }

                if (false === @preg_match('/' . $allowOrigin . '/', '')) {
                    throw new InvalidArgumentException(
                        [ 'Invalid AllowOrigin: %s', $allowOrigin ]
                    );
                }

                $this->allowOrigins[ $allowOrigin ] = true;
            }
        }

        return $this;
    }


    /**
     * @param null|string|iterable $allowHeaders
     *
     * @return static
     */
    public function allowHeaders($allowHeaders)
    {
        if (null === $allowHeaders) {
            $this->allowHeaders = [];

        } else {
            $allowHeaders = is_iterable($allowHeaders)
                ? $allowHeaders
                : [ $allowHeaders ];

            foreach ( $allowHeaders as $allowHeader ) {
                $value = strtolower($allowHeader);

                if (! Helper::filterRegexShort($value)) {
                    $value = preg_quote($value, '/');
                }

                if (false === @preg_match('/' . $value . '/', '')) {
                    throw new InvalidArgumentException(
                        [ 'Invalid AllowHeader: %s', $value ]
                    );
                }

                if (isset(static::$publicHeaders[ $value ])) {
                    continue;
                }

                $this->allowHeaders[ $value ] = true;
            }
        }

        return $this;
    }

    /**
     * @param null|string|iterable $exposeHeaders
     *
     * @return static
     */
    public function exposeHeaders($exposeHeaders)
    {
        if (null === $exposeHeaders) {
            $this->exposeHeaders = [];

        } else {
            $exposeHeaders = is_iterable($exposeHeaders)
                ? $exposeHeaders
                : [ $exposeHeaders ];

            foreach ( $exposeHeaders as $exposeHeader ) {
                $value = strtolower($exposeHeader);

                if (! Helper::filterRegexShort($value)) {
                    $value = preg_quote($value, '/');
                }

                if (false === @preg_match('/' . $value . '/', '')) {
                    throw new InvalidArgumentException(
                        [ 'Invalid ExposeHeader: %s', $value ]
                    );
                }

                if (isset(static::$publicHeaders[ $value ])) {
                    continue;
                }

                $this->exposeHeaders[ $value ] = true;
            }
        }

        return $this;
    }


    /**
     * @param null|bool $allowCredentials
     *
     * @return static
     */
    public function allowCredentials(bool $allowCredentials = null)
    {
        $this->allowCredentials = $allowCredentials;

        return $this;
    }


    /**
     * @param null|int $maxAge
     *
     * @return static
     */
    public function maxAge(int $maxAge = null)
    {
        if (null !== $maxAge) {
            $maxAge = max(0, $maxAge);
        }

        $this->maxAge = $maxAge;

        return $this;
    }


    /**
     * @var bool[]
     */
    protected static $publicHeaders = [
        'cache-control'    => true,
        'content-language' => true,
        'content-length'   => true,
        'content-type'     => true,
        'expires'          => true,
        'last-modified'    => true,
        'pragma'           => true,
    ];
}
