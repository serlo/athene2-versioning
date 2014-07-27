<?php

namespace Versioning\Options;

use Versioning\Exception;
use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * @package Versioning\Options
 * @author Aeneas Rekkas
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

    public function getPermission($repository, $action)
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
