<?php

namespace Versioning\Manager;

/**
 * Class RepositoryManagerAwareTrait
 *
 * @package Versioning\Manager
 * @author Aeneas Rekkas
 */
trait RepositoryManagerAwareTrait
{

    /**
     * The RepositoryManager
     *
     * @var RepositoryManagerInterface
     */
    protected $repositoryManager;

    /**
     * Gets the RepositoryManager
     *
     * @return RepositoryManagerInterface $repositoryManager
     */
    public function getRepositoryManager()
    {
        return $this->repositoryManager;
    }

    /**
     * Sets the RepositoryManager
     *
     * @param RepositoryManagerInterface $repositoryManager
     * @return self
     */
    public function setRepositoryManager(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
        return $this;
    }
}
