<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Exceptions;

/**
 *
 */
class RepositoryException extends \Exception
{

    /**
     * RepositoryException constructor.
     *
     * @param $message
     * @param int $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }


    /**
     * custom string representation of object
     *
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}