<?php

namespace MichaelRubel\EnhancedContainer\Concerns;

use Illuminate\Support\Str;
use MichaelRubel\EnhancedContainer\Traits\HelpsProxies;

class MethodForwarder implements MethodForwarding
{
    use HelpsProxies;

    /**
     * @param string $class
     * @param array  $dependencies
     */
    public function __construct(
        private string $class,
        private array $dependencies
    ) {
    }

    /**
     * Forward the method.
     *
     * @return object
     * @throws \ReflectionException
     */
    public function forward(): object
    {
        return rescue(
            fn () => $this->resolvePassedClass($this->forwardsTo(), $this->dependencies),
            fn ($e) => throw new \BadMethodCallException($e->getMessage()),
            false
        );
    }

    /**
     * Parse the class where to forward the call.
     *
     * @return string
     */
    public function forwardsTo(): string
    {
        return collect(
            take($this->class)
                ->pipe(fn ($class) => explode(self::CLASS_SEPARATOR, $class))
                ->pipe(
                    fn ($delimited) => collect($delimited)->map(
                        fn ($item) => str_replace(
                            Str::{config('enhanced-container.naming')}(config('enhanced-container.from')),
                            Str::{config('enhanced-container.naming')}(config('enhanced-container.to')),
                            $item
                        )
                    )
                )->pipe(
                    fn ($structure) => implode(
                        self::CLASS_SEPARATOR,
                        collect(
                            $structure->put(
                                $structure->keys()->last(),
                                str_replace(
                                    config('enhanced-container.from'),
                                    config('enhanced-container.to'),
                                    $structure->last() ?? ''
                                )
                            )
                        )->toArray()
                    )
                )->get()
        )->first();
    }
}
