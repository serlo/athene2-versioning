<?php

namespace Athene2\Versioning;

/**
 * Class Module
 *
 * @package Versioning
 * @author Aeneas Rekkas
 */
class Module
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }
}
