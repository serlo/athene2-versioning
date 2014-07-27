<?php

namespace CommonTest;

use Versioning\Module;

/**
 * Class ModuleTest
 *
 * @package CommonTest
 * @author Aeneas Rekkas
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigIsArray()
    {
        $module = new Module();
        $this->assertInternalType('array', $module->getConfig());
    }
}
