<?php

namespace VersioningTest\Manager;

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use Versioning\Event\VersioningEvent;
use Versioning\Manager\RepositoryManagerInterface;
use VersioningTest\Asset\RepositoryFake;
use VersioningTest\Asset\RevisionFake;
use Versioning\Manager\RepositoryManager;
use Zend\EventManager\EventManager;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Versioning\Options\ModuleOptions;

/**
 * Class RepositoryManagerTest
 *
 * @package VersioningTest\Manager
 * @author  Aeneas Rekkas
 */
class RepositoryManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryManager
     */
    protected $repositoryManager;

    /**
     * @var AuthorizationService|Mock
     */
    protected $authorizationService;

    /**
     * @var ObjectManager|Mock
     */
    protected $objectManager;

    /**
     * @var ModuleOptions|Mock
     */
    protected $moduleOptions;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $moduleConfig = [
        'permissions' => [
            'VersioningTest\Asset\RepositoryFake' => [
                ModuleOptions::KEY_PERMISSION_CHECKOUT => 'checkout',
                ModuleOptions::KEY_PERMISSION_COMMIT   => 'commit',
                ModuleOptions::KEY_PERMISSION_REJECT   => 'reject'
            ]
        ]
    ];

    public function testFlush()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $objectManager->expects($this->once())->method('flush');
        $repositoryManager->flush();
    }

    public function testCommitRevisionWithoutPermission()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $repository           = new RepositoryFake();
        $eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');

        $authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $authorizationService->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $objectManager->expects($this->never())->method('persist');
        $eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::COMMIT_UNAUTHORIZED);
        $this->setExpectedException('ZfcRbac\Exception\UnauthorizedException');

        $repositoryManager->setEventManager($eventManager);
        $repositoryManager->commitRevision($repository, []);
    }

    public function testRejectRevisionWithoutPermission()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $repository           = new RepositoryFake();
        $revision             = new RevisionFake();
        $eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');

        $authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $authorizationService->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $objectManager->expects($this->never())->method('persist');
        $eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::REJECT_UNAUTHORIZED);
        $this->setExpectedException('ZfcRbac\Exception\UnauthorizedException');

        $repositoryManager->setEventManager($eventManager);
        $repositoryManager->rejectRevision($repository, $revision);
    }

    public function testCheckoutRevisionWithoutPermission()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $repository           = new RepositoryFake();
        $revision             = new RevisionFake();
        $eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');

        $authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $authorizationService->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $objectManager->expects($this->never())->method('persist');
        $eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::CHECKOUT_UNAUTHORIZED);
        $this->setExpectedException('ZfcRbac\Exception\UnauthorizedException');

        $repositoryManager->setEventManager($eventManager);
        $repositoryManager->checkoutRevision($repository, $revision);
    }

    public function testCheckoutRevision()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $repository           = new RepositoryFake();
        $revision             = new RevisionFake();
        $eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $message              = 'foobar';
        $repositoryConfig     = $this->moduleConfig['permissions']['VersioningTest\Asset\RepositoryFake'];
        $permission           = $repositoryConfig[ModuleOptions::KEY_PERMISSION_CHECKOUT];

        $authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $authorizationService->expects($this->once())->method('isGranted')->with($permission, $revision)
            ->will($this->returnValue(true));
        $objectManager->expects($this->once())->method('persist')->with($repository);
        $eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::CHECKOUT);

        $repositoryManager->setEventManager($eventManager);
        $repositoryManager->checkoutRevision($repository, $revision, $message);
    }

    public function testRejectRevision()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $repository           = new RepositoryFake();
        $revision             = new RevisionFake();
        $eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $message              = 'foobar';
        $repositoryConfig     = $this->moduleConfig['permissions']['VersioningTest\Asset\RepositoryFake'];
        $permission           = $repositoryConfig[ModuleOptions::KEY_PERMISSION_REJECT];

        $authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $authorizationService->expects($this->once())->method('isGranted')->with($permission, $revision)
            ->will($this->returnValue(true));
        $objectManager->expects($this->once())->method('persist')->with($revision);
        $eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::REJECT);

        $repositoryManager->setEventManager($eventManager);
        $repositoryManager->rejectRevision($repository, $revision, $message);
        $this->assertTrue($revision->isTrashed());
    }

    public function testCommitRevision()
    {
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $moduleOptions        = new ModuleOptions($this->moduleConfig);
        $repositoryManager    = new RepositoryManager($authorizationService, $moduleOptions, $objectManager);
        $repository           = new RepositoryFake();
        $eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $message              = 'foobar';
        $repositoryConfig     = $this->moduleConfig['permissions']['VersioningTest\Asset\RepositoryFake'];
        $permission           = $repositoryConfig[ModuleOptions::KEY_PERMISSION_COMMIT];
        $data                 = ['foo' => 'bar'];

        $authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $authorizationService->expects($this->once())->method('isGranted')->with($permission)
            ->will($this->returnValue(true));
        $objectManager->expects($this->atLeastOnce())->method('persist');

        $eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::COMMIT);
        $repositoryManager->setEventManager($eventManager);
        $revision = $repositoryManager->commitRevision($repository, $data, $message);

        $this->assertSame($repository, $revision->getRepository());
        $this->assertSame($identity, $revision->getAuthor());
        $this->assertSame('bar', $revision->get('foo'));
    }
}
