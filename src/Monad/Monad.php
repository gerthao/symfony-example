<?php

declare(strict_types=1);

namespace App\Monad;

/**
 * @template T
 */
interface Monad
{
    /**
     * @param T $value
     * @return Monad<T>
     */
    public static function unit($value): Monad;

    /**
     * @template U
     * @param callable(T): Monad<U> $func
     * @return Monad<U>
     */
    public function flatMap(callable $func): Monad;

}