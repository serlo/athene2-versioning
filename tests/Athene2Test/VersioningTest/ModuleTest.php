<?php

namespace Athene2Test\VersioningTest;

use Athene2\Versioning\Module;

/**
 * Class ModuleTest
 *
 * @package VersioningTest
 * @author  Aeneas Rekkas
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigIsArray()
    {
        $module = new Module();
        $this->assertInternalType('array', $module->getConfig());
    }
}
