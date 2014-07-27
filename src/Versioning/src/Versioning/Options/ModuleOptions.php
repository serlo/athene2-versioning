<?php

namespace Versioning\Options;

use Versioning\Exception;
use Zend\Stdlib\AbstractOptions;
use Versioning\Entity\RepositoryInterface;

/**
 * Class ModuleOptions
 *
 * @package Versioning\Options
 * @author  Aeneas Rekkas
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param RepositoryInterface $repository
     * @param string              $action
     * @return string
     * @throws Exception\RuntimeException
     */
    public function getPermission(RepositoryInterface $repository, $action)
    {
        $className = get_class($repository);

        if (!isset($this->permissions[$className])) {
            throw new Exception\RuntimeException(sprintf('Permission for repository "%s" not found', $className));
        }

        if (!isset($this->permissions[$className][$action])) {
            throw new Exception\RuntimeException(sprintf(
                'Permission action "%s" for object "%s" not found',
                $action,
                $className
            ));
        }

        return $this->permissions[$className][$action];
    }
}
