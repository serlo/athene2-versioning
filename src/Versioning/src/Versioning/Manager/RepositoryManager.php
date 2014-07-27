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
        if (!$revision instanceof RevisionInterface) {
            $revision = $this->findRevision($repository, $revision);
        }

        $user       = $this->authorizationService->getIdentity();
        $permission = $this->moduleOptions->getPermission($repository, 'checkout');
        $this->assertGranted($permission, $repository);
        $repository->setCurrentRevision($revision);

        $this->getEventManager()->trigger(
            'checkout',
            $this,
            [
                'repository' => $repository,
                'revision'   => $revision,
                'actor'      => $user,
                'reason'     => $reason
            ]
        );

        $this->objectManager->persist($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function commitRevision(RepositoryInterface $repository, array $data)
    {
        $author     = $this->authorizationService->getIdentity();
        $permission = $this->moduleOptions->getPermission($repository, 'commit');
        $revision   = $repository->createRevision();

        $this->assertGranted($permission, $repository);
        $revision->setAuthor($author);
        $repository->addRevision($revision);
        $revision->setRepository($repository);

        foreach ($data as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $revision->set($key, $value);
            }
        }

        $this->getEventManager()->trigger(
            'commit',
            $this,
            [
                'repository' => $repository,
                'revision'   => $revision,
                'data'       => $data,
                'author'     => $author
            ]
        );

        $this->objectManager->persist($revision);

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
        if (!$revision instanceof RevisionInterface) {
            $revision = $this->findRevision($repository, $revision);
        }

        $user       = $this->authorizationService->getIdentity();
        $permission = $this->moduleOptions->getPermission($repository, 'reject');

        $this->assertGranted($permission, $repository);
        $revision->setTrashed(true);
        $this->objectManager->persist($revision);
        $this->getEventManager()->trigger(
            'reject',
            $this,
            [
                'repository' => $repository,
                'revision'   => $revision,
                'actor'      => $user,
                'reason'     => $reason
            ]
        );
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
        if ($this->authorizationService->isGranted($permission, $context)) {
            throw new UnauthorizedException('You have no permission to access this method.');
        }
    }
}
