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

namespace CommonTest\Manager;

use Versioning\Manager\RepositoryManager;
use ZfcRbac\Service\AuthorizationService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Versioning\Options\ModuleOptions;

class RepositoryManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryManager
     */
    protected $repositoryManager;

    /**
     * @var AuthorizationService|Mock
     */
    protected $authorizationService;

    /**
     * @var ObjectManager|Mock
     */
    protected $objectManager;

    /**
     * @var ModuleOptions|Mock
     */
    protected $moduleOptions;

    public function setUp()
    {
        $this->moduleOptions        = $this->getMock('Versioning\Options\ModuleOptions');
        $this->authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);
        $this->objectManager        = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $this->repositoryManager    = new RepositoryManager($this->authorizationService, $this->moduleOptions, $this->objectManager);
    }

    public function testFlush()
    {
        $this->objectManager->expects($this->once())->method('flush');
        $this->repositoryManager->flush();
    }
}
