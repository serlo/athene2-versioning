<?php

namespace Athene2Test\VersioningTest\Manager;

use Athene2Test\VersioningTest\Asset\RepositoryManagerFactoryFake;

class RepositoryManagerFactoryTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRepositoryManager()
    {
        $factory        = new RepositoryManagerFactoryFake();
        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager');
        $manager        = $this->getMock('Athene2\Versioning\Manager\RepositoryManager', [], [], '', false);

        $serviceManager->expects($this->once())->method('get')->with('Athene2\Versioning\Manager\RepositoryManager')
            ->will($this->returnValue($manager));

        $this->assertSame($manager, $factory->test($serviceManager));
    }
}
 