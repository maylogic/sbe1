<?php

namespace App\Traits;

trait MemoizeMethods
{
    private array $__memoizedResults = [];

    public function memoize(string $name, callable $callback)
    {
        if (!array_key_exists($name, $this->__memoizedResults)) {
            $this->__memoizedResults[$name] = call_user_func($callback);
        }
        return $this->__memoizedResults[$name];
    }
}
