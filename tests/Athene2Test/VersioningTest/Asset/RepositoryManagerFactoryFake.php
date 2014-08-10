<?php

namespace Athene2Test\VersioningTest\Asset;

use Athene2\Versioning\Manager\RepositoryManagerFactoryTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RepositoryManagerFactoryFake
 *
 * @package VersioningTest\Asset
 * @author  Aeneas Rekkas
 */
class RepositoryManagerFactoryFake
{
    use RepositoryManagerFactoryTrait;

    public function test(ServiceLocatorInterface $serviceLocator)
    {
        return $this->getRepositoryManager($serviceLocator);
    }
}
