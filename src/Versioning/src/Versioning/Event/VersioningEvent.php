<?php

namespace Versioning\Event;

use Zend\EventManager\Event;
use Versioning\Entity\RevisionInterface;
use Versioning\Manager\RepositoryManagerInterface;
use Versioning\Entity\RepositoryInterface;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Class VersioningEvent
 *
 * @package Versioning\Event
 * @author  Aeneas Rekkas
 */
class VersioningEvent extends Event
{
    /**
     * @var string
     */
    const COMMIT_UNAUTHORIZED = 'commit.unauthorized';

    /**
     * @var string
     */
    const REJECT_UNAUTHORIZED = 'reject.unauthorized';

    /**
     * @var string
     */
    const CHECKOUT_UNAUTHORIZED = 'checkout.unauthorized';

    /**
     * @var string
     */
    const COMMIT = 'commit';

    /**
     * @var string
     */
    const REJECT = 'reject';

    /**
     * @var string
     */
    const CHECKOUT = 'checkout';

    /**
     * @var RevisionInterface
     */
    protected $revision;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var RepositoryManagerInterface
     */
    protected $repositoryManager;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var IdentityInterface
     */
    protected $identity;

    public function __construct(
        IdentityInterface $identity,
        RepositoryInterface $repository,
        RevisionInterface $revision,
        RepositoryManagerInterface $repositoryManager,
        $message = '',
        $data = []
    ) {
        $this->identity          = $identity;
        $this->repository        = $repository;
        $this->revision          = $revision;
        $this->repositoryManager = $repositoryManager;
        $this->message           = $message;
        $this->data              = $data;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return IdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return RepositoryManagerInterface
     */
    public function getRepositoryManager()
    {
        return $this->repositoryManager;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return RevisionInterface
     */
    public function getRevision()
    {
        return $this->revision;
    }
}
 