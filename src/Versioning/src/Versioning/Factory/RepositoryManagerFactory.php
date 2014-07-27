<?php

namespace Versioning\Factory;

use Common\Factory\AuthorizationServiceFactoryTrait;
use Common\Factory\EntityManagerFactoryTrait;
use Versioning\Manager\RepositoryManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Versioning\Options\ModuleOptions;

/**
 * Class RepositoryManagerFactory
 *
 * @package Versioning\Factory
 * @author  Aeneas Rekkas
 */
class RepositoryManagerFactory implements FactoryInterface
{
    use AuthorizationServiceFactoryTrait, EntityManagerFactoryTrait;

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $moduleOptions ModuleOptions */
        $moduleOptions        = $serviceLocator->get('Versioning\Options\ModuleOptions');
        $objectManager        = $this->getEntityManager($serviceLocator);
        $authorizationService = $this->getAuthorizationService($serviceLocator);

        return new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
    }
}
