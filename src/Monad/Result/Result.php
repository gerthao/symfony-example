<?php

namespace App\Monad\Result;

use Exception;
use JiriPudil\SealedClasses\Sealed;

/**
 * @template T
 */
#[Sealed([Success::class, Failure::class])]
readonly abstract class Result
{
    /**
     * @param T|Exception $value
     */
    protected function __construct(public mixed $value)
    {
    }

    /**
     * @template U
     * @param callable(T): U $func
     * @return Result<U>
     */
    public function map(callable $func): Result
    {
        return $this->isSuccess() ? Result::unit(fn() => $func($this->value)) : new Failure($this->value);
    }

    public function isSuccess(): bool
    {
        return !($this->value instanceof Exception);
    }

    /**
     * @template U
     * @param callable(): U $func
     * @return Result<U>
     */
    public static function unit(callable $func): Result
    {
        try {
            $value = $func();
            return new Success($value);
        } catch (Exception $e) {
            return new Failure($e);
        }
    }

    /**
     * @template U
     * @param U $default
     * @param callable(T): U $func
     * @return U
     */
    public function fold(mixed $default, callable $func): mixed
    {
        return $this->isFailure() ? $default : $func($this->value);
    }

    public function isFailure(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * @template U
     * @param callable(T): Result<U> $func
     * @return Result<U>
     */
    public function flatMap(callable $func): Result
    {
        return $this->isFailure() ? new Failure($this->value) : $func($this->value);
    }

    /**
     * @template U
     * @param callable(Exception): U $func
     * @return Result<U>
     */
    public function recover(callable $func): Result
    {
        return $this->isFailure() ? Result::unit(fn() => $func($this->value)) : $this;
    }
}