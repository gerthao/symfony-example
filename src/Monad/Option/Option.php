<?php

namespace App\Monad\Option;


use App\Monad\Monad;
use JiriPudil\SealedClasses\Sealed;
use Throwable;

/**
 * @template T
 * @implements Monad<T>
 */
#[Sealed(permits: [None::class, Some::class])]
readonly abstract class Option implements Monad
{
    /**
     * @param T|null $value
     */
    protected function __construct(public mixed $value)
    {
    }

    /**
     * @template U
     * @param bool $cond
     * @param U $value
     * @return Option<U>
     */
    public static function cond(bool $cond, mixed $value): Option
    {
        return $cond ? new Some($value) : new None();
    }

    /**
     * @template U of T
     * @param U $default
     * @return U
     */
    public function getOrElse(mixed $default)
    {
        return !isset($this->value) ? $default : $this->value;
    }

    /**
     * @returns T
     * @throws Throwable
     */
    public function getOrThrow(Throwable $error)
    {
        if (isset($this->value)) return $this->value;
        throw new $error;
    }

    /**
     * @template U
     * @param callable(T): Option<U> $func
     * @return Option<U>
     */
    public function flatMap(callable $func): Option
    {
        return !isset($this->value) ? new None() : $func($this->value);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !isset($this->value);
    }

    /**
     * @template U
     * @param callable(T): U $func
     * @return Option<U>
     */
    public function map(callable $func): Option
    {
        return !isset($this->value) ? new None() : Option::unit($func($this->value));
    }

    /**
     * @template A
     * @param A|null $value
     * @return Option<A>
     */
    public static function unit(mixed $value): Option
    {
        return !isset($value) ? new None() : new Some($value);
    }

    /**
     * @template U
     * @param U $op
     * @param callable(T): U $func
     * @return U
     */
    public function fold($op, callable $func): mixed
    {
        return !isset($this->value) ? $op : $func($this->value);
    }

    /**
     * @param callable(T): mixed $func
     * @return void
     */
    public function forEach(callable $func): void
    {
        if (isset($this->value)) $func($this->value);
    }

    /**
     * @param callable(T): bool $predicate
     * @return Option<T>
     */
    public function find(callable $predicate): Option
    {
        return $this->filter($predicate);
    }

    /**
     * @param callable(T): bool $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): Option
    {
        if (!isset($this->value) || !$predicate($this->value)) {
            return new None();
        } else {
            return new Some($this->value);
        }
    }

    /**
     * @param callable(T): bool $predicate
     * @return bool
     */
    public function exists(callable $predicate): bool
    {
        return $this->isDefined() && $predicate($this->value);
    }

    /**
     * @return bool
     */
    public function isDefined(): bool
    {
        return isset($this->value);
    }
}

