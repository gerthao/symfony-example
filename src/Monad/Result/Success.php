<?php

namespace App\Monad\Result;

/**
 * @template T
 * @extends Result<T>
 */
readonly class Success extends Result
{
    public function __construct(mixed $value)
    {
        parent::__construct($value);
    }
}