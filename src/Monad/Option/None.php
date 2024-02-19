<?php

namespace App\Monad\Option;

/**
 * @extends Option<null>
 */
readonly class None extends Option
{
    public function __construct()
    {
        parent::__construct(null);
    }
}