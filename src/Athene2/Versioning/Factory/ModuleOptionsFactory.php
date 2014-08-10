<?php

namespace Athene2\Versioning\Factory;

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
     * {@inheritDoc}
     */
    protected function getClassName()
    {
        return 'Athene2\Versioning\Options\ModuleOptions';
    }

    /**
     * {@inheritDoc}
     */
    protected function getKeyName()
    {
        return 'versioning';
    }
}
