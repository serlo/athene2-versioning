<?php

namespace Athene2\Versioning\Entity;

/**
 * Interface RepositoryInterface
 *
 * @package Versioning\Entity
 * @author Aeneas Rekkas
 */
interface RepositoryInterface
{
    /**
     * @param RevisionInterface $revision
     * @return void
     */
    public function addRevision(RevisionInterface $revision);

    /**
     * Creates a new revision
     *
     * @return RevisionInterface
     */
    public function createRevision();

    /**
     * @return RevisionInterface
     */
    public function getCurrentRevision();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * Returns the revisions
     *
     * @return RevisionInterface[]
     */
    public function getRevisions();

    /**
     * @return bool
     */
    public function hasCurrentRevision();

    /**
     * @param RevisionInterface $revision
     * @return void
     */
    public function removeRevision(RevisionInterface $revision);

    /**
     * @param RevisionInterface $revision
     * @return void
     */
    public function setCurrentRevision(RevisionInterface $revision);
}
