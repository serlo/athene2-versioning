<?php

namespace Versioning\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use Versioning\Options\ModuleOptions;
use Zend\EventManager\EventManagerAwareTrait;
use ZfcRbac\Exception\UnauthorizedException;
use ZfcRbac\Service\AuthorizationService;
use Versioning\Exception;

/**
 * Class RepositoryManager
 *
 * @package Versioning\Manager
 * @author  Aeneas Rekkas
 */
class RepositoryManager implements RepositoryManagerInterface
{
    use EventManagerAwareTrait;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @param AuthorizationService $authorizationService
     * @param ModuleOptions        $moduleOptions
     * @param ObjectManager        $objectManager
     */
    public function __construct(
        AuthorizationService $authorizationService,
        ModuleOptions $moduleOptions,
        ObjectManager $objectManager
    ) {
        $this->moduleOptions        = $moduleOptions;
        $this->objectManager        = $objectManager;
        $this->authorizationService = $authorizationService;
    }

    /**
     * {@inheritDoc}
     */
    public function checkoutRevision(RepositoryInterface $repository, $revision, $reason = '')
    {
        $this->handleRevision($repository, $revision, $reason, 'checkout');
    }

    /**
     * {@inheritDoc}
     */
    public function commitRevision(RepositoryInterface $repository, array $data)
    {
        $this->hasPermission($repository, 'commit');

        $author   = $this->authorizationService->getIdentity();
        $revision = $repository->createRevision();

        $revision->setAuthor($author);
        $repository->addRevision($revision);
        $revision->setRepository($repository);

        foreach ($data as $key => $value) {
            $revision->set($key, $value);
        }

        $this->objectManager->persist($revision);
        $this->triggerEvent('commit', $repository, $revision, null, $data);

        return $revision;
    }

    /**
     * {@inheritDoc}
     */
    public function findRevision(RepositoryInterface $repository, $id)
    {
        foreach ($repository->getRevisions() as $revision) {
            if ($revision->getId() == $id) {
                return $revision;
            }
        }

        throw new Exception\RevisionNotFoundException(sprintf('Revision "%d" not found', $id));
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function rejectRevision(RepositoryInterface $repository, $revision, $reason = '')
    {
        $this->handleRevision($repository, $revision, $reason, 'reject');
    }

    /**
     * {@inheritDoc}
     */
    protected function handleRevision(RepositoryInterface $repository, $revision, $reason = '', $key)
    {
        $this->hasPermission($repository, $key);

        if (!$revision instanceof RevisionInterface) {
            $revision = $this->findRevision($repository, $revision);
        }

        if ($key == 'reject') {
            $revision->setTrashed(true);
            $this->objectManager->persist($revision);
        } else {
            $repository->setCurrentRevision($revision);
            $this->objectManager->persist($repository);
        }
        $this->triggerEvent($key, $repository, $revision, $reason);
    }

    /**
     * @param RepositoryInterface $repository
     * @param string              $key
     * @return void
     */
    protected function hasPermission(RepositoryInterface $repository, $key)
    {
        $permission = $this->moduleOptions->getPermission($repository, $key);
        $this->assertGranted($permission, $repository);
    }

    /**
     * @param string              $event
     * @param RepositoryInterface $repository
     * @param RevisionInterface   $revision
     * @param string|null         $reason
     * @param array|null          $data
     * @return void
     */
    protected function triggerEvent(
        $event,
        RepositoryInterface $repository,
        RevisionInterface $revision,
        $reason = null,
        $data = null
    ) {
        $identity = $this->authorizationService->getIdentity();
        $params   = [
            'repository' => $repository,
            'revision'   => $revision
        ];

        if ($event == 'commit') {
            $params['author'] = $identity;
        } else {
            $params['actor'] = $identity;
        }

        if ($reason) {
            $params['reason'] = $reason;
        }

        if ($data) {
            $params['data'] = $data;
        }

        $this->getEventManager()->trigger($event, $this, $params);
    }

    /**
     * Throws an exception if a permission isn't granted
     *
     * @param string $permission
     * @param mixed  $context
     * @throws UnauthorizedException
     */
    protected function assertGranted($permission, $context = null)
    {
        if (!$this->authorizationService->isGranted($permission, $context)) {
            throw new UnauthorizedException(sprintf('You are missing permission %s.', $permission));
        }
    }
}
