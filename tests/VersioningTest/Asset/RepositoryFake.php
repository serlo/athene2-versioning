<?php
namespace VersioningTest\Asset;

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;

/**
 * Class RepositoryFake
 *
 * @package VersioningTest\Asset
 * @author Aeneas Rekkas
 */
class RepositoryFake implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function addRevision(RevisionInterface $revision)
    {
        // TODO: Implement addRevision() method.
    }

    /**
     * {@inheritDoc}
     */
    public function createRevision()
    {
        // TODO: Implement createRevision() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentRevision()
    {
        // TODO: Implement getCurrentRevision() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getRevisions()
    {
        // TODO: Implement getRevisions() method.
    }

    /**
     * {@inheritDoc}
     */
    public function hasCurrentRevision()
    {
        // TODO: Implement hasCurrentRevision() method.
    }

    /**
     * {@inheritDoc}
     */
    public function removeRevision(RevisionInterface $revision)
    {
        // TODO: Implement removeRevision() method.
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentRevision(RevisionInterface $revision)
    {
        // TODO: Implement setCurrentRevision() method.
    }

}
