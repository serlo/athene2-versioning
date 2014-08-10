<?php

namespace Athene2Test\VersioningTest\Manager;

use Athene2Test\VersioningTest\Asset\RepositoryManagerAwareFake;

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
        $manager = $this->getMock('Athene2\Versioning\Manager\RepositoryManager', [], [], '', false);

        $trait->setRepositoryManager($manager);
        $this->assertSame($manager, $trait->getRepositoryManager());
    }
}
