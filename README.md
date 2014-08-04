athene2-versioning
========================

[![Build Status](https://travis-ci.org/serlo-org/athene2-versioning.svg)](https://travis-ci.org/serlo-org/athene2-versioning)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/serlo-org/athene2-versioning/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/serlo-org/athene2-versioning/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/serlo-org/athene2-versioning/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/serlo-org/athene2-versioning/?branch=master)

# Installation

Install this module by adding

`"serlo-org/athene2-versioning": "~1.0",`

to your composer.json.

# Using the versioning module

The versioning module enables you to manage repositories which contain revisions. Each repository has **n** revisions and one or zero HEAD revision. The HEAD revision is the current revision of that repository. The default implementation of the VersioningManager is **[Doctrine friendly](http://www.doctrine-project.org/)**!

## Understanding how it works

The versioning module consists of one `Versioning\Manager\VersioningManager`, who implements the `Versioning\Manager\VersioningManagerInterface`. The `Versioning\Entity\VersioningManager` manages models or entities which implement the `Versioning\Entity\RepositoryInterface` and the `Versioning\Entity\RevisionInterface`.

### Let's implement those entity interfaces!

#### RevisionInterface

```php
<?php

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Class Revision
 *
 * @author Aeneas Rekkas
 */
class Revision implements RevisionInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var
     */
    protected $author;

    /**
     * @var bool
     */
    protected $trashed = false;

    /**
     * @var array
     */
    protected $data = [];

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
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthor(IdentityInterface $author)
    {
        $this->author = $author;
    }

    /**
     * {@inheritDoc}
     */
    public function setTrashed($trash)
    {
        $this->trashed = (bool)$trash;
    }

    /**
     * {@inheritDoc}
     */
    public function isTrashed()
    {
        return $this->trashed;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        isset($this->data[$key]) ? $this->data[$key] : null;
    }
}

```

#### RepositoryInterface

```
<?php

use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;

class Repository implements RepositoryInterface
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
        return new Revision();
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

```

Well, that wasn't so hard, was it?
