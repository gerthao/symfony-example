<?php

namespace App\Monad;

/**
 * @template T
 */
trait Zippable
{
    public abstract function zip($e): Monad;
}