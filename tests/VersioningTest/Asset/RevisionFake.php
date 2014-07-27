<?php

namespace VersioningTest\Asset;

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Class RevisionFake
 *
 * @package CommonTest\Asset
 * @author  Aeneas Rekkas
 */
class RevisionFake implements RevisionInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var
     */
    protected $author;

    /**
     * @var
     */
    protected $repository;

    /**
     * Returns the ID of this object
     *
     * @return mixed
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

    /**
     * Returns the repository
     *
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Sets the repository
     *
     * @param RepositoryInterface $repository
     * @return void
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Gets the author
     *
     * @return IdentityInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author
     *
     * @param IdentityInterface $user
     * @return void
     */
    public function setAuthor(IdentityInterface $user)
    {
        $this->author = $user;
    }

    /**
     * Sets the trashed attribute
     *
     * @param bool $trash
     * @return void
     */
    public function setTrashed($trash)
    {
        // TODO: Implement setTrashed() method.
    }

    /**
     * Returns if this revision is trashed or not
     *
     * @return bool
     */
    public function isTrashed()
    {
        // TODO: Implement isTrashed() method.
    }

    /**
     * Sets a value
     *
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Gets a value
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->data[$key];
    }
}
