<?php

declare(strict_types=1);

namespace HappyInc\Worker;

final class Context
{
    /**
     * @psalm-var array<class-string<ContextData>, ContextData>
     */
    private array $data = [];

    /**
     * @template T of ContextData
     * @psalm-param class-string<T> $class
     * @psalm-return T
     */
    public function dataOf(string $class): ContextData
    {
        /** @psalm-var T */
        return $this->data[$class] ??= new $class();
    }
}
