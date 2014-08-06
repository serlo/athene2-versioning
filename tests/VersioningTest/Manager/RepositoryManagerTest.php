<?php

namespace VersioningTest\Manager;

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use Versioning\Event\VersioningEvent;
use VersioningTest\Asset\RepositoryFake;
use VersioningTest\Asset\RevisionFake;
use Versioning\Manager\RepositoryManager;
use Zend\EventManager\EventManager;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Versioning\Options\ModuleOptions;
use Zend\EventManager\EventManagerInterface;

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
     * @var EventManagerInterface|Mock
     */
    protected $eventManager;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var RevisionInterface
     */
    protected $revision;

    /**
     * @var IdentityInterface|Mock
     */
    protected $identity;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var bool
     */
    protected $eventCalled = false;

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

    public function setUp()
    {
        $this->authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $this->objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $this->moduleOptions        = new ModuleOptions($this->moduleConfig);
        $this->repositoryManager    = new RepositoryManager($this->authorizationService, $this->moduleOptions, $this->objectManager);
        $this->repository           = new RepositoryFake();
        $this->revision             = new RevisionFake();
        $this->eventManager         = $this->getMock('Zend\EventManager\EventManagerInterface');
        $this->identity             = $this->getMock('ZfcRbac\Identity\IdentityInterface');

        $this->repositoryManager->setEventManager($this->eventManager);
    }

    public function testCommitRevisionWithoutPermissionTriggersEventAndThrowsException()
    {
        $this->authorizationService->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $this->objectManager->expects($this->never())->method('persist');
        $this->eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::COMMIT_UNAUTHORIZED);
        $this->setExpectedException('ZfcRbac\Exception\UnauthorizedException');

        $this->repositoryManager->commitRevision($this->repository, []);
    }

    public function testRejectRevisionWithoutPermissionTriggersEventAndThrowsException()
    {
        $this->authorizationService->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $this->objectManager->expects($this->never())->method('persist');
        $this->eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::REJECT_UNAUTHORIZED);
        $this->setExpectedException('ZfcRbac\Exception\UnauthorizedException');

        $this->repositoryManager->setEventManager($this->eventManager);
        $this->repositoryManager->rejectRevision($this->repository, $this->revision);
    }

    public function testCheckoutRevisionWithoutPermissionTriggersEventAndThrowsException()
    {
        $this->authorizationService->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $this->objectManager->expects($this->never())->method('persist');
        $this->eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::CHECKOUT_UNAUTHORIZED);
        $this->setExpectedException('ZfcRbac\Exception\UnauthorizedException');

        $this->repositoryManager->setEventManager($this->eventManager);
        $this->repositoryManager->checkoutRevision($this->repository, $this->revision);
    }

    public function testCheckoutRevision()
    {
        $repositoryConfig = $this->moduleConfig['permissions']['VersioningTest\Asset\RepositoryFake'];
        $permission       = $repositoryConfig[ModuleOptions::KEY_PERMISSION_CHECKOUT];

        $this->authorizationService->expects($this->once())->method('isGranted')->with($permission, $this->revision)
            ->will($this->returnValue(true));
        $this->objectManager->expects($this->once())->method('persist')->with($this->repository);
        $this->eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::CHECKOUT);

        $this->repositoryManager->setEventManager($this->eventManager);
        $this->repositoryManager->checkoutRevision($this->repository, $this->revision);
    }

    public function testFindRevision()
    {
        $repository = new RepositoryFake();
        $revision   = new RevisionFake();
        $id         = 5;

        $revision->setId($id);
        $repository->addRevision($revision);

        $this->authorizationService->expects($this->any())->method('isGranted')->will($this->returnValue(true));

        $this->assertEquals($revision, $this->repositoryManager->findRevision($repository, $id));
        $this->assertEquals($revision, $this->repositoryManager->findRevision($repository, $revision));
    }

    public function testRejectRevision()
    {
        $repositoryConfig = $this->moduleConfig['permissions']['VersioningTest\Asset\RepositoryFake'];
        $permission       = $repositoryConfig[ModuleOptions::KEY_PERMISSION_REJECT];

        $this->authorizationService->expects($this->once())->method('isGranted')->with($permission, $this->revision)
            ->will($this->returnValue(true));
        $this->objectManager->expects($this->once())->method('persist')->with($this->revision);
        $this->eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::REJECT);

        $this->repositoryManager->setEventManager($this->eventManager);
        $this->repositoryManager->rejectRevision($this->repository, $this->revision);
        $this->assertTrue($this->revision->isTrashed());
    }

    public function testCommitRevision()
    {
        $repositoryConfig = $this->moduleConfig['permissions']['VersioningTest\Asset\RepositoryFake'];
        $permission       = $repositoryConfig[ModuleOptions::KEY_PERMISSION_COMMIT];
        $data             = ['foo' => 'bar'];

        $this->authorizationService->expects($this->any())->method('getIdentity')->will(
            $this->returnValue($this->identity)
        );
        $this->authorizationService->expects($this->once())->method('isGranted')->with($permission)
            ->will($this->returnValue(true));
        $this->objectManager->expects($this->atLeastOnce())->method('persist');

        $this->eventManager->expects($this->once())->method('trigger')->with(VersioningEvent::COMMIT);
        $this->repositoryManager->setEventManager($this->eventManager);

        $revision = $this->repositoryManager->commitRevision($this->repository, $data);

        $this->assertSame($this->repository, $revision->getRepository());
        $this->assertSame($this->identity, $revision->getAuthor());
        $this->assertSame('bar', $revision->get('foo'));
        $this->assertEquals($revision, current($this->repository->getRevisions()));
    }

    public function testEventHydration()
    {
        $this->eventManager = new EventManager();

        $this->authorizationService->expects($this->any())->method('getIdentity')->will(
            $this->returnValue($this->identity)
        );
        $this->authorizationService->expects($this->any())->method('isGranted')->will($this->returnValue(true));

        $this->eventManager->attach(VersioningEvent::COMMIT, [$this, 'eventHydrationCallbackTest']);
        $this->eventManager->attach(VersioningEvent::REJECT, [$this, 'eventHydrationCallbackTest']);
        $this->eventManager->attach(VersioningEvent::CHECKOUT, [$this, 'eventHydrationCallbackTest']);
        $this->repositoryManager->setEventManager($this->eventManager);

        $this->repositoryManager->commitRevision($this->repository, [], 'foo');
        $this->assertTrue($this->eventCalled);
        $this->eventCalled = false;

        $this->repositoryManager->checkoutRevision($this->repository, $this->revision, 'foo');
        $this->assertTrue($this->eventCalled);
        $this->eventCalled = false;

        $this->repositoryManager->rejectRevision($this->repository, $this->revision, 'foo');
        $this->assertTrue($this->eventCalled);
        $this->eventCalled = false;
    }

    public function eventHydrationCallbackTest(VersioningEvent $event)
    {
        $this->assertInstanceOf('ZfcRbac\Identity\IdentityInterface', $event->getIdentity());
        $this->assertInstanceOf('Versioning\Entity\RevisionInterface', $event->getRevision());
        $this->assertInstanceOf('Versioning\Entity\RepositoryInterface', $event->getRepository());
        $this->assertSame('foo', $event->getMessage());
        $this->eventCalled = true;
    }
}
