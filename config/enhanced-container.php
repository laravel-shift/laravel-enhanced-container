<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Method Forwarding
    |--------------------------------------------------------------------------
    |
    | This feature will forward your call to the secondary service automatically
    | if the target class doesn't contain the method was called. You're
    | able to configure method forwarding directories to meet your application
    | structure.
    |
    */

    /*
     * Determine if you want to use method forwarding.
     */

    'forwarding_enabled' => false,

    /*
     * This option activates manual forwarding in case if the
     * CallProxy cannot perform the call through the Container.
     *
     * Pay attention that if you're accessing the method that doesn't exist
     * in the class by inheritance, but uses magic methods to access, you'll
     * be unable to use method binding on such methods.
     */

    'manual_forwarding' => true,

    /*
     | "From"
     |
     | The layer to forward from.
     |
     | Available naming options: plural, pluralStudly, singular, studly,
     | any other string conversion from "Illuminate\Support\Str"
     |
     | Default: 'pluralStudly'
     */

    'from' => [
        'layer'  => 'Service',
        'naming' => 'pluralStudly',
    ],

    /*
     | "To"
     |
     | The layer where the call is forwarded.
     |
     | Available naming options: plural, pluralStudly, singular, studly,
     | any other string conversion from "Illuminate\Support\Str"
     |
     | Default: 'pluralStudly'
     */

    'to' => [
        'layer'   => 'Repository',
        'naming'  => 'pluralStudly',
        'postfix' => true,
    ],

];
