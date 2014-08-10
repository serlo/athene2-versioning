<?php

namespace Athene2\Versioning\Manager;

use Common\ObjectManager\Flushable;
use Athene2\Versioning\Entity\RepositoryInterface;
use Athene2\Versioning\Entity\RevisionInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Athene2\Versioning\Exception;

/**
 * Interface RepositoryManagerInterface
 *
 * @package Versioning\Manager
 * @author  Aeneas Rekkas
 */
interface RepositoryManagerInterface extends EventManagerAwareInterface, Flushable
{
    /**
     * Check out a revision.
     * <code>
     * $repositoryManager->checkoutRevision($repository, 123, "my reason");
     * </code>
     *
     * @param RepositoryInterface     $repository
     * @param mixed|RevisionInterface $revision
     * @param string                  $message
     * @return mixed
     * @throws Exception\RevisionNotFoundException
     */
    public function checkoutRevision(RepositoryInterface $repository, $revision, $message = '');

    /**
     * Creates a new revision and adds it to the repository.
     * <code>
     * $repositoryManager->commitRevision($repository, ['foo' => 'bar', 'acme' => 'bar'], "my reason");
     * </code>
     *
     * @param RepositoryInterface $repository
     * @param array               $data
     * @param string              $message
     * @return RevisionInterface
     */
    public function commitRevision(RepositoryInterface $repository, array $data, $message = '');

    /**
     * Finds an revision by its id.
     * <code>
     * $repositoryManager->findRevision($repository, 123);
     * </code>
     *
     * @param RepositoryInterface     $repository
     * @param mixed|RevisionInterface $revision
     * @return RevisionInterface
     * @throws Exception\RevisionNotFoundException
     */
    public function findRevision(RepositoryInterface $repository, $revision);


    /**
     * Rejects a revision (opposite of checkoutRevision).
     * <code>
     * $repositoryManager->rejectRevision($repository, 123, 'That's spam...');
     * </code>
     *
     * @param RepositoryInterface   $repository
     * @param int|RevisionInterface $revision
     * @param string                $message
     * @return void
     */
    public function rejectRevision(RepositoryInterface $repository, $revision, $message = '');
}
