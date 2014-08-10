<?php

namespace Athene2\Versioning\Factory;

use Common\Factory\AuthorizationServiceFactoryTrait;
use Common\Factory\EntityManagerFactoryTrait;
use Athene2\Versioning\Manager\RepositoryManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Athene2\Versioning\Options\ModuleOptions;
use ZfcRbac\Service\AuthorizationService;
use Doctrine\Common\Persistence\ObjectManager;

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
        $moduleOptions = $serviceLocator->get('Athene2\Versioning\Options\ModuleOptions');
        /* @var $objectManager ObjectManager */
        $objectManager = $this->getEntityManager($serviceLocator);
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $this->getAuthorizationService($serviceLocator);

        return new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
    }
}
