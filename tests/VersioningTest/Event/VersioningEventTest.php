<?php

namespace VersioningTest\Event;

use Versioning\Event\VersioningEvent;
use VersioningTest\Asset\RepositoryFake;

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
        $repositoryManager = $this->getMock('Versioning\Manager\RepositoryManager', [], [], '', false);
        $repository        = $this->getMock('Versioning\Entity\RepositoryInterface');
        $revision          = $this->getMock('Versioning\Entity\RevisionInterface');
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
 