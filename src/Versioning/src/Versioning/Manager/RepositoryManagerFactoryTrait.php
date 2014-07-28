<?php

namespace Versioning\Manager;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RepositoryManagerFactoryTrait
 *
 * @package Versioning\Manager
 * @author  Aeneas Rekkas
 */
trait RepositoryManagerFactoryTrait
{
    /**
     * Returns the RepositoryManager
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return RepositoryManagerInterface
     */
    protected function getRepositoryManager(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $repositoryManager RepositoryManagerInterface */
        $repositoryManager = $serviceLocator->get('Versioning\Manager\RepositoryManager');
        return $repositoryManager;
    }
}
