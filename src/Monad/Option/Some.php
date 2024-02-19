<?php

namespace App\Monad\Option;

/**
 * @template T
 * @extends Option<T>
 */
readonly class Some extends Option
{
    /**
     * @param T $value
     */
    public function __construct($value)
    {
        parent::__construct($value);
    }
}