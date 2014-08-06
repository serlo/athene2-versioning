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
     * @var string
     */
    const KEY_PERMISSION_COMMIT = 'commit';

    /**
     * @var string
     */
    const KEY_PERMISSION_REJECT = 'reject';

    /**
     * @var string
     */
    const KEY_PERMISSION_CHECKOUT = 'checkout';

    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * @param array $permissions
     * @throws Exception\RuntimeException
     */
    public function setPermissions(array $permissions)
    {
        foreach ($permissions as $className => $data) {
            $this->testKey(self::KEY_PERMISSION_COMMIT, $className, $data);
            $this->testKey(self::KEY_PERMISSION_CHECKOUT, $className, $data);
            $this->testKey(self::KEY_PERMISSION_REJECT, $className, $data);
        }

        $this->permissions = $permissions;
    }

    /**
     * @param string $key
     * @param string $className
     * @param array  $permissions
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function testKey($key, $className, array $permissions)
    {
        if (!isset($permissions[$key])) {
            $message = sprintf('Permission key "%s" for repository "%s" is missing.', $key, $className);
            throw new Exception\RuntimeException($message);
        }
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
     * @param string              $key
     * @return string
     * @throws Exception\RuntimeException
     */
    public function getPermission(RepositoryInterface $repository, $key)
    {
        $className = get_class($repository);

        if (!isset($this->permissions[$className])) {
            throw new Exception\RuntimeException(sprintf('Permission for repository "%s" not found', $className));
        } elseif (!isset($this->permissions[$className][$key])) {
            throw new Exception\RuntimeException(sprintf(
                'Permission action "%s" for object "%s" not found',
                $key,
                $className
            ));
        }

        return $this->permissions[$className][$key];
    }
}
