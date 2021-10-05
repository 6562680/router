<?php

namespace Gzhegow\Router\Domain\Cors;


/**
 * Cors
 */
class Cors
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
     * @var null|int
     */
    protected $maxAge;


    /**
     * Constructor
     *
     * @param null|array $allowOrigins
     *
     * @param null|array $allowHeaders
     * @param null|array $exposeHeaders
     *
     * @param null|bool  $allowCredentials
     *
     * @param null|int   $maxAge
     */
    public function __construct(
        array $allowOrigins = null,

        array $allowHeaders = null,
        array $exposeHeaders = null,

        bool $allowCredentials = null,

        int $maxAge = null
    )
    {
        $this->allowOrigins = $allowOrigins;

        $this->allowHeaders = $allowHeaders;
        $this->exposeHeaders = $exposeHeaders;

        $this->allowCredentials = $allowCredentials;

        $this->maxAge = $maxAge;
    }


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return array_filter([
            'allowOrigins'     => $this->allowOrigins,
            'allowHeaders'     => $this->allowHeaders,
            'exposeHeaders'    => $this->exposeHeaders,
            'allowCredentials' => $this->allowCredentials,
            'maxAge'           => $this->maxAge,
        ], function ($v) { return ! is_null($v); });
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data) : void
    {
        $this->allowOrigins = $data[ 'allowOrigins' ] ?? [];
        $this->allowHeaders = $data[ 'allowHeaders' ] ?? [];
        $this->exposeHeaders = $data[ 'exposeHeaders' ] ?? [];
        $this->allowCredentials = $data[ 'allowCredentials' ] ?? null;
        $this->maxAge = $data[ 'maxAge' ] ?? null;
    }


    /**
     * @return null|array
     */
    public function getAllowOrigins() : ?array
    {
        return $this->allowOrigins;
    }


    /**
     * @return null|array
     */
    public function getAllowHeaders() : ?array
    {
        return $this->allowHeaders;
    }

    /**
     * @return null|array
     */
    public function getExposeHeaders() : ?array
    {
        return $this->exposeHeaders;
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
}
