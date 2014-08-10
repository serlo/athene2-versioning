<?php

namespace Athene2Test\VersioningTest\Options;

use Athene2\Versioning\Manager\RepositoryManagerInterface;
use Athene2\Versioning\Options\ModuleOptions;
use Athene2Test\VersioningTest\Asset\RepositoryFake;

/**
 * Class ModuleOptionsTest
 *
 * @package VersioningTest\Options
 * @author  Aeneas Rekkas
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $moduleConfig = [
        'permissions' => [
            'Athene2Test\VersioningTest\Asset\RepositoryFake' => [
                ModuleOptions::KEY_PERMISSION_CHECKOUT => 'checkout',
                ModuleOptions::KEY_PERMISSION_COMMIT   => 'commit',
                ModuleOptions::KEY_PERMISSION_REJECT   => 'reject'
            ]
        ]
    ];

    public function testGetPermissions()
    {
        $options = new ModuleOptions($this->moduleConfig);
        $this->assertEquals($this->moduleConfig['permissions'], $options->getPermissions());
    }

    public function testSetPermissions()
    {
        $options = new ModuleOptions([]);
        $options->setPermissions($this->moduleConfig['permissions']);
        $this->assertEquals($this->moduleConfig['permissions'], $options->getPermissions());
    }

    public function testGetPermission()
    {
        $repository = new RepositoryFake();
        $options    = new ModuleOptions($this->moduleConfig);
        $key        = ModuleOptions::KEY_PERMISSION_CHECKOUT;
        $this->assertEquals('checkout', $options->getPermission($repository, $key));
        $key = ModuleOptions::KEY_PERMISSION_COMMIT;
        $this->assertEquals('commit', $options->getPermission($repository, $key));
        $key = ModuleOptions::KEY_PERMISSION_REJECT;
        $this->assertEquals('reject', $options->getPermission($repository, $key));
    }

    public function testThrowsExceptionWhenInvalidConfigIsGiven()
    {
        $options = new ModuleOptions([]);
        $this->setExpectedException('Athene2\Versioning\Exception\RuntimeException');
        $options->setPermissions(['permissions' => []]);
    }

    public function testThrowsExceptionWhenPermissionIsNotFound()
    {
        $repository = new RepositoryFake();
        $options    = new ModuleOptions($this->moduleConfig);
        $this->setExpectedException('Athene2\Versioning\Exception\RuntimeException');
        $this->assertEquals('checkout', $options->getPermission($repository, 'doesNotExist'));
    }

    public function testThrowsExceptionWhenConfigIsNotFound()
    {
        $repository = new RepositoryFake();
        $options    = new ModuleOptions([]);
        $key        = ModuleOptions::KEY_PERMISSION_REJECT;
        $this->setExpectedException('Athene2\Versioning\Exception\RuntimeException');
        $this->assertEquals('checkout', $options->getPermission($repository, $key));
    }
}
 