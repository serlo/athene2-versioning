<?php

namespace Versioning;

/**
 * @author Aeneas Rekkas
 */
return [
    'service_manager' => [
        'factories' => [
            __NAMESPACE__ . '\Options\ModuleOptions'     => __NAMESPACE__ . '\Factory\ModuleOptionsFactory',
            __NAMESPACE__ . '\Manager\RepositoryManager' => __NAMESPACE__ . '\Factory\RepositoryManagerFactory'
        ]
    ],
    'di'              => [
        'instance' => [
            'preferences' => [
                'Versioning\Manager\RepositoryManagerInterface' => 'Versioning\RepositoryManager'
            ],
        ]
    ]
];
