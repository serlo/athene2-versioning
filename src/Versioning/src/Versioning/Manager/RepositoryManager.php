<?php

namespace Versioning\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use Versioning\Event\VersioningEvent;
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
    public function checkoutRevision(RepositoryInterface $repository, $revision, $message = '')
    {
        $this->handleRevision($repository, $revision, $message, VersioningEvent::CHECKOUT);
    }

    /**
     * {@inheritDoc}
     */
    public function commitRevision(RepositoryInterface $repository, array $data, $message = '')
    {
        $author   = $this->authorizationService->getIdentity();
        $revision = $repository->createRevision();

        $this->hasPermission($repository, $revision, VersioningEvent::COMMIT);

        $repository->addRevision($revision);
        $revision->setRepository($repository);

        if ($author) {
            $revision->setAuthor($author);
        }

        foreach ($data as $key => $value) {
            $revision->set($key, $value);
        }

        $this->objectManager->persist($revision);
        $this->objectManager->persist($repository);
        $this->triggerEvent(VersioningEvent::COMMIT, $repository, $revision, $message, $data);

        return $revision;
    }

    /**
     * {@inheritDoc}
     */
    public function findRevision(RepositoryInterface $repository, $revision)
    {
        if ($revision instanceof RevisionInterface) {
            return $revision;
        }

        foreach ($repository->getRevisions() as $current) {
            if ($current->getId() == $revision) {
                return $current;
            }
        }

        throw new Exception\RevisionNotFoundException(sprintf('Revision with id "%d" not found.', $revision));
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
    public function rejectRevision(RepositoryInterface $repository, $revision, $message = '')
    {
        $this->handleRevision($repository, $revision, $message, VersioningEvent::REJECT);
    }


    /**
     * @param RepositoryInterface     $repository
     * @param mixed|RevisionInterface $revision
     * @param string                  $message
     * @param string                  $event
     * @return void
     */
    protected function handleRevision(RepositoryInterface $repository, $revision, $message, $event)
    {
        $revision = $this->findRevision($repository, $revision);

        $this->hasPermission($repository, $revision, $event);

        $revision->setRepository($repository);
        $repository->addRevision($revision);

        if ($event === VersioningEvent::REJECT) {
            $revision->setTrashed(true);
            $this->objectManager->persist($revision);
        } else {
            $repository->setCurrentRevision($revision);
            $this->objectManager->persist($repository);
        }

        $this->triggerEvent($event, $repository, $revision, $message);
    }

    /**
     * @param RepositoryInterface $repository
     * @param RevisionInterface   $revision
     * @param string              $event
     * @return void
     * @throws UnauthorizedException
     */
    protected function hasPermission(RepositoryInterface $repository, RevisionInterface $revision, $event)
    {
        $permission = $this->moduleOptions->getPermission($repository, $event);

        if (!$this->authorizationService->isGranted($permission, $revision)) {

            switch ($event) {
                case VersioningEvent::REJECT:
                    $event = VersioningEvent::REJECT_UNAUTHORIZED;
                    break;
                case VersioningEvent::COMMIT:
                    $event = VersioningEvent::COMMIT_UNAUTHORIZED;
                    break;
                case VersioningEvent::CHECKOUT:
                    $event = VersioningEvent::CHECKOUT_UNAUTHORIZED;
                    break;
            }

            $this->triggerEvent($event, $repository, $revision);

            throw new UnauthorizedException(sprintf(
                'You are missing permission "%s" for this event %s.',
                $permission,
                $event
            ));
        }
    }

    /**
     * @param string              $eventName
     * @param RepositoryInterface $repository
     * @param RevisionInterface   $revision
     * @param string              $message
     * @param array               $data
     * @return void
     */
    protected function triggerEvent(
        $eventName,
        RepositoryInterface $repository,
        RevisionInterface $revision,
        $message = '',
        $data = []
    ) {
        $identity = $this->authorizationService->getIdentity();
        $event    = new VersioningEvent($repository, $revision, $this, $message, $data, $identity);
        $event->setName($eventName);
        $this->getEventManager()->trigger($eventName, $this, $event);
    }
}
