<?php

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Class Revision
 *
 * @author Aeneas Rekkas
 */
class RevisionExample implements RevisionInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var
     */
    protected $author;

    /**
     * @var bool
     */
    protected $trashed = false;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param mixed $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthor(IdentityInterface $author)
    {
        $this->author = $author;
    }

    /**
     * {@inheritDoc}
     */
    public function setTrashed($trash)
    {
        $this->trashed = (bool)$trash;
    }

    /**
     * {@inheritDoc}
     */
    public function isTrashed()
    {
        return $this->trashed;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
