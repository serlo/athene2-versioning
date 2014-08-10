<?php

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Athene2\Versioning\Event\VersioningEvent;

class SharedListenerExample implements SharedListenerAggregateInterface
{
    public function attachShared(SharedEventManagerInterface $events)
    {
        $class = 'Athene2\Versioning\Manager\RepositoryManager';

        // Looks like everything worked out fine!
        $events->attach($class, VersioningEvent::COMMIT, [$this, 'onCommit']);
        $events->attach($class, VersioningEvent::CHECKOUT, [$this, 'onCheckout']);
        $events->attach($class, VersioningEvent::REJECT, [$this, 'onReject']);

        // These events get triggered, when authorization was not granted.
        // $events->attach($class, VersioningEvent::COMMIT_UNAUTHORIZED, [$this, 'onCommitError']);
        // $events->attach($class, VersioningEvent::CHECKOUT_UNAUTHORIZED, [$this, 'onCheckoutError']);
        // $events->attach($class, VersioningEvent::REJECT_UNAUTHORIZED, [$this, 'onRejectError']);
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        // ...
    }

    public function onCommit(VersioningEvent $e)
    {
        $repositoryManager = $e->getRepositoryManager();
        $repository        = $e->getRepository();
        $revision          = $e->getRevision();
        $data              = $e->getData();
        $message           = $e->getMessage();
        $identity          = $e->getIdentity();

        // ...
    }

    public function onCheckout(VersioningEvent $e)
    {
        $repositoryManager = $e->getRepositoryManager();
        $repository        = $e->getRepository();
        $revision          = $e->getRevision();
        $data              = $e->getData();
        $message           = $e->getMessage();
        $identity          = $e->getIdentity();

        // ...
    }

    public function onReject(VersioningEvent $e)
    {
        $repositoryManager = $e->getRepositoryManager();
        $repository        = $e->getRepository();
        $revision          = $e->getRevision();
        $data              = $e->getData();
        $message           = $e->getMessage();
        $identity          = $e->getIdentity();

        // ...
    }
}
