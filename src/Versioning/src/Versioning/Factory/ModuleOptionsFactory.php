<?php

namespace Versioning\Factory;

use Common\Factory\AbstractOptionsFactory;

/**
 * Class ModuleOptionsFactory
 *
 * @package Versioning\Factory
 * @author  Aeneas Rekkas
 */
class ModuleOptionsFactory extends AbstractOptionsFactory
{
    /**
     * @return string
     */
    protected function getClassName()
    {
        return 'Versioning\Options\ModuleOptions';
    }

    /**
     * @return string
     */
    protected function getKeyName()
    {
        return 'versioning';
    }
}
