<?php

namespace App\Monad\Result;

use Exception;

/**
 * @template T of Exception
 * @extends Result<T>
 */
readonly class Failure extends Result
{
    public function __construct(Exception $exception)
    {
        parent::__construct($exception);
    }
}