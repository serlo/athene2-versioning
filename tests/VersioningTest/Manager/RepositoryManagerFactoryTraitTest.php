<?php

namespace VersioningTest\Manager;

use VersioningTest\Asset\RepositoryManagerFactoryFake;

class RepositoryManagerFactoryTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRepositoryManager()
    {
        $factory        = new RepositoryManagerFactoryFake();
        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager');
        $manager        = $this->getMock('Versioning\Manager\RepositoryManager', [], [], '', false);

        $serviceManager->expects($this->once())->method('get')->with('Versioning\Manager\RepositoryManager')
            ->will($this->returnValue($manager));

        $this->assertSame($manager, $factory->test($serviceManager));
    }
}
 