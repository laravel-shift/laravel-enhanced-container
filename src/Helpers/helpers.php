<?php

declare(strict_types=1);

use MichaelRubel\EnhancedContainer\Call;
use MichaelRubel\EnhancedContainer\Concerns\BindingBuilding;

if (! function_exists('call')) {
    /**
     * @param string|object $class
     * @param array         $parameters
     *
     * @return mixed
     */
    function call(string|object $class, array $parameters = []): mixed
    {
        return app(Call::class, [$class, $parameters]);
    }
}

if (! function_exists('bind')) {
    /**
     * @param string|object $class
     *
     * @return mixed
     */
    function bind(string|object $class): mixed
    {
        return app(BindingBuilding::class, [$class]);
    }
}
