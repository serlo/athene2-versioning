<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */

namespace VersioningTest\Manager;

use VersioningTest\Asset\RevisionFake;
use Versioning\Manager\RepositoryManager;
use ZfcRbac\Service\AuthorizationService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Versioning\Options\ModuleOptions;

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

    public function setUp()
    {
        $this->moduleOptions        = $this->getMock('Versioning\Options\ModuleOptions');
        $this->authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $this->objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $this->repositoryManager    = new RepositoryManager($this->authorizationService, $this->moduleOptions, $this->objectManager);
    }

    public function testFlush()
    {
        $this->objectManager->expects($this->once())->method('flush');
        $this->repositoryManager->flush();
    }

    public function testCheckoutRevision()
    {
        $repository   = $this->getMock('Versioning\Entity\RepositoryInterface');
        $revision     = $this->getMock('Versioning\Entity\RevisionInterface');
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $identity     = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $reason       = 'foobar';
        $permission   = 'myPermission';


        $repository->expects($this->once())->method('setCurrentRevision');
        $this->authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $this->moduleOptions->expects($this->once())->method('getPermission')->with($repository, 'checkout')
            ->will($this->returnValue($permission));
        $this->authorizationService->expects($this->once())->method('isGranted')->with($permission, $repository)
            ->will($this->returnValue(true));
        $eventManager->expects($this->once())->method('trigger')->with('checkout', $this->repositoryManager, [
            'repository' => $repository,
            'revision'   => $revision,
            'reason'     => $reason,
            'actor'      => $identity
        ]);
        $this->objectManager->expects($this->once())->method('persist')->with($repository);

        $this->repositoryManager->setEventManager($eventManager);
        $this->repositoryManager->checkoutRevision($repository, $revision, $reason);
    }

    public function testRejectRevision()
    {
        $repository   = $this->getMock('Versioning\Entity\RepositoryInterface');
        $revision     = $this->getMock('Versioning\Entity\RevisionInterface');
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $identity     = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $reason       = 'foobar';
        $permission   = 'myPermission';


        $revision->expects($this->once())->method('setTrashed')->with(true);
        $this->authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $this->moduleOptions->expects($this->once())->method('getPermission')->with($repository, 'reject')
            ->will($this->returnValue($permission));
        $this->authorizationService->expects($this->once())->method('isGranted')->with($permission, $repository)
            ->will($this->returnValue(true));
        $eventManager->expects($this->once())->method('trigger')->with('reject', $this->repositoryManager, [
            'repository' => $repository,
            'revision'   => $revision,
            'reason'     => $reason,
            'actor'      => $identity
        ]);
        $this->objectManager->expects($this->once())->method('persist')->with($revision);

        $this->repositoryManager->setEventManager($eventManager);
        $this->repositoryManager->rejectRevision($repository, $revision, $reason);
    }

    public function testCommitRevision()
    {
        $repository   = $this->getMock('Versioning\Entity\RepositoryInterface');
        $revision     = new RevisionFake();
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $identity     = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $data         = ['acme' => 123, 'bar' => 'foo', 'foo' => 'bar'];
        $permission   = 'myPermission';


        $repository->expects($this->once())->method('createRevision')->will($this->returnValue($revision));
        $repository->expects($this->once())->method('addRevision')->with($revision);
        $this->authorizationService->expects($this->any())->method('getIdentity')->will($this->returnValue($identity));
        $this->moduleOptions->expects($this->once())->method('getPermission')->with($repository, 'commit')
            ->will($this->returnValue($permission));
        $this->authorizationService->expects($this->once())->method('isGranted')->with($permission, $repository)
            ->will($this->returnValue(true));
        $eventManager->expects($this->once())->method('trigger')->with('commit', $this->repositoryManager, [
            'repository' => $repository,
            'revision'   => $revision,
            'data'       => $data,
            'author'     => $identity
        ]);
        $this->objectManager->expects($this->once())->method('persist')->with($revision);

        $this->repositoryManager->setEventManager($eventManager);
        $this->repositoryManager->commitRevision($repository, $data);

        $this->assertSame($identity, $revision->getAuthor());
        $this->assertSame($repository, $revision->getRepository());
        $this->assertEquals(123, $revision->get('acme'));
        $this->assertEquals('foo', $revision->get('bar'));
        $this->assertEquals('bar', $revision->get('foo'));
    }
}
