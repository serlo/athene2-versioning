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

namespace Athene2Test\VersioningTest\Factory;

use Athene2\Versioning\Factory\ModuleOptionsFactory;
use Athene2Test\VersioningTest\Asset\RepositoryFake;
use Athene2\Versioning\Options\ModuleOptions;

class ModuleOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryFake
     */
    protected $repository;

    public function testCreateService()
    {
        $config      = ['versioning' => []];
        $service     = $this->executePermissionTest($config);

        $this->assertInstanceOf('Athene2\Versioning\Options\ModuleOptions', $service);
        $this->assertEquals([] , $service->getPermissions());
    }

    protected function executePermissionTest($config = null)
    {
        $this->repository = new RepositoryFake();
        $serviceManager   = $this->getMock('Zend\ServiceManager\ServiceManager');
        $factory          = new ModuleOptionsFactory();

        $serviceManager->expects($this->once())->method('get')->with('config')->will($this->returnValue($config));

        /* @var $service ModuleOptions */
        $service = $factory->createService($serviceManager);
        return $service;
    }
}
 