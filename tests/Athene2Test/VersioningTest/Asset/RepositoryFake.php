<?php
namespace Athene2Test\VersioningTest\Asset;

use Athene2\Versioning\Entity\RepositoryInterface;
use Athene2\Versioning\Entity\RevisionInterface;

/**
 * Class RepositoryFake
 *
 * @package VersioningTest\Asset
 * @author  Aeneas Rekkas
 */
class RepositoryFake implements RepositoryInterface
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
        return new RevisionFake();
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
