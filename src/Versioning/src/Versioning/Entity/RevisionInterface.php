<?php

namespace Versioning\Entity;

use ZfcRbac\Identity\IdentityInterface;

/**
 * Interface RevisionInterface
 *
 * @package Versioning\Entity
 * @author  Aeneas Rekkas
 */
interface RevisionInterface
{
    /**
     * Returns the ID of this object
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns the repository
     *
     * @return RepositoryInterface
     */
    public function getRepository();

    /**
     * Sets the repository
     *
     * @param RepositoryInterface $repository
     * @return void
     */
    public function setRepository(RepositoryInterface $repository);

    /**
     * Sets the author
     *
     * @param IdentityInterface $user
     * @return void
     */
    public function setAuthor(IdentityInterface $user);

    /**
     * Sets the trashed attribute
     *
     * @param bool $trash
     * @return void
     */
    public function setTrashed($trash);

    /**
     * Returns if this revision is trashed or not
     *
     * @return bool
     */
    public function isTrashed();

    /**
     * Sets a value
     *
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Gets a value
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key);
}
