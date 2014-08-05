<?php

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;

/**
 * Class Repository
 *
 * @author Aeneas Rekkas
 */
class RepositoryExample implements RepositoryInterface
{
    /**
     * @var array|RevisionInterface[]
     */
    protected $revisions = [];

    /**
     * @var null|RevisionInterface
     */
    protected $head = null;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * {@inheritDoc}
     */
    public function addRevision(RevisionInterface $revision)
    {
        $this->revisions[$revision->getId()] = $revision;
    }

    /**
     * {@inheritDoc}
     */
    public function createRevision()
    {
        return new Revision();
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentRevision()
    {
        return $this->head;
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
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * {@inheritDoc}
     */
    public function hasCurrentRevision()
    {
        return null !== $this->head;
    }

    /**
     * {@inheritDoc}
     */
    public function removeRevision(RevisionInterface $revision)
    {
        unset($this->revisions[$revision->getId()]);
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentRevision(RevisionInterface $revision)
    {
        $this->head = $revision;
    }
}
