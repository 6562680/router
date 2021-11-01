<?php


namespace Gzhegow\Router\Exceptions;


/**
 * Exception
 */
class Exception extends \Exception
{
    /**
     * @var mixed
     */
    protected $payload;


    /**
     * Constructor
     *
     * @param string          $message
     * @param null            $payload
     * @param null|\Throwable $previous
     */
    public function __construct($message = "", $payload = null, \Throwable $previous = null)
    {
        $this->payload = $payload;

        $message = is_iterable($message)
            ? $message
            : [ $message ];

        $replacements = $message;
        $text = array_shift($replacements);

        foreach ( $replacements as $i => $replacement ) {
            $replacements[ $i ] = null
                ?? ( is_null($replacement) ? '{ null }' : null )
                ?? ( is_scalar($replacement) ? var_export($replacement, 1) : null )
                ?? ( is_array($replacement) ? vsprintf('{ Array(%d) : %s }', [
                    count($replacement),
                    preg_replace('/\s+/', ' ', trim(var_export($replacement, 1))),
                ]) : null )
                ?? ( is_object($replacement) ? '{ ' . get_class($replacement) . ' #' . spl_object_id($replacement) . ' }' : null );
        }

        $text = sprintf($text, ...$replacements);

        parent::__construct($text, null, $previous);
    }


    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
