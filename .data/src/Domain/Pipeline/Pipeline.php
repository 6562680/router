<?php


namespace Gzhegow\Router\Domain\Pipeline;

use Psr\Http\Message\MessageInterface;
use Gzhegow\Router\Domain\Command\CommandHandlerInterface;


/**
 * Pipeline
 */
class Pipeline
{
    /**
     * @var CommandHandlerInterface[]
     */
    protected $pipes = [];


    /**
     * @param MessageInterface $message
     *
     * @return null|int|mixed
     */
    public function handle(MessageInterface $message)
    {
        $result = $message;

        foreach ( $this->pipes as $pipe ) {
            $result = $pipe->handle($result);
        }

        return $result;
    }


    /**
     * @param $pipe
     *
     * @return static
     */
    public function pipe($pipe)
    {
        $this->pipes[] = $pipe;

        return $this;
    }
}
