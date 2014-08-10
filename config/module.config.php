<?php

namespace Athene2\Versioning;

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
                'Athene2\Versioning\Manager\RepositoryManagerInterface' => 'Athene2\Versioning\RepositoryManager'
            ],
        ]
    ]
];
