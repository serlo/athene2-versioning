<?php

namespace VersioningTest\Manager;

use VersioningTest\Asset\RepositoryManagerAwareFake;

/**
 * Class RepositoryManagerAwareTrait
 *
 * @package VersioningTest\Manager
 * @author  Aeneas Rekkas
 */
class RepositoryManagerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetRepositoryManager()
    {
        $trait   = new RepositoryManagerAwareFake();
        $manager = $this->getMock('Versioning\Manager\RepositoryManager', [], [], '', false);

        $trait->setRepositoryManager($manager);
        $this->assertSame($manager, $trait->getRepositoryManager());
    }
}
