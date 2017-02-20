<?php return [
    // This contains the Laravel Packages that you want this plugin to utilize listed under their package identifiers
    'packages' => [
        'yajra/laravel-datatables-oracle' => [
            // Service providers to be registered by your plugin
            'providers' => [
                '\Yajra\Datatables\DatatablesServiceProvider',
            ],

            // Aliases to be registered by your plugin in the form of $alias => $pathToFacade
            'aliases' => [
                'Datatables' => '\Yajra\Datatables\Facades\Datatables',
            ],

            // The namespace to set the configuration under. For this example, this package accesses it's config via config('purifier.' . $key), so the namespace 'purifier' is what we put here
            'config_namespace' => 'datatables',

            // The configuration file for the package itself. Start this out by copying the default one that comes with the package and then modifying what you need.
            'config' => [
            ],
        ],
    ],
];
