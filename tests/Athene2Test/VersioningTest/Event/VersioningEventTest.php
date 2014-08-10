<?php

namespace Athene2Test\VersioningTest\Event;

use Athene2\Versioning\Event\VersioningEvent;

/**
 * Class VersioningEventTest
 *
 * @package VersioningTest\Event
 * @author  Aeneas Rekkas
 */
class VersioningEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $repositoryManager = $this->getMock('Athene2\Versioning\Manager\RepositoryManager', [], [], '', false);
        $repository        = $this->getMock('Athene2\Versioning\Entity\RepositoryInterface');
        $revision          = $this->getMock('Athene2\Versioning\Entity\RevisionInterface');
        $identity          = $this->getMock('ZfcRbac\Identity\IdentityInterface');
        $message           = 'foobar';
        $data              = ['foo' => 'bar'];

        $event = new VersioningEvent($repository, $revision, $repositoryManager, $message, $data, $identity);

        $this->assertSame($identity, $event->getIdentity());
        $this->assertSame($repository, $event->getRepository());
        $this->assertSame($revision, $event->getRevision());
        $this->assertSame($repositoryManager, $event->getRepositoryManager());
        $this->assertEquals($message, $event->getMessage());
        $this->assertEquals($data, $event->getData());
    }
}
