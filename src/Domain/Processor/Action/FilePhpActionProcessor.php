<?php


namespace Gzhegow\Router\Domain\Processor\Action;

use Gzhegow\Router\Utils;


/**
 * FilePhpActionProcessor
 */
class FilePhpActionProcessor implements ActionProcessorInterface
{
    /**
     * @param mixed $action
     * @param mixed $payload
     * @param mixed ...$arguments
     *
     * @return null|int|mixed|void
     */
    public function processAction($action, $payload, ...$arguments)
    {
        $realpath = Utils::thePathFilePhpVal($action);

        $requireReturn = require $realpath;

        $result = $result ?? $requireReturn;

        return $result;
    }


    /**
     * @param mixed $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        return Utils::filterFilePhp($action);
    }
}
