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

namespace VersioningTest\Factory;


use Versioning\Factory\ModuleOptionsFactory;
use VersioningTest\Asset\RepositoryFake;
use Versioning\Options\ModuleOptions;

class ModuleOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryFake
     */
    protected $repository;

    public function testCreateService()
    {
        $permissions = [
            'VersioningTest\Asset\RepositoryFake' => [
                'checkout' => 'checkout.permission'
            ]
        ];
        $config      = [
            'versioning' => [
                'permissions' => $permissions
            ]
        ];
        $service     = $this->executePermissionTest($config);

        $this->assertInstanceOf('Versioning\Options\ModuleOptions', $service);
        $this->assertEquals($permissions, $service->getPermissions());
        $this->assertEquals('checkout.permission', $service->getPermission($this->repository, 'checkout'));
    }

    public function testCreateServiceWithoutConfig()
    {
        $this->setExpectedException('Versioning\Exception\RuntimeException');

        $service = $this->executePermissionTest();

        $this->assertInstanceOf('Versioning\Options\ModuleOptions', $service);
        $this->assertEquals([], $service->getPermissions());
        $this->assertEquals('checkout.permission', $service->getPermission($this->repository, 'checkout'));
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
 