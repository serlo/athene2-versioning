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

# Features

* Doctrine implementation (using the ObjectManager)
* Bundled with [zfc-rbac](https://github.com/ZF-Commons/zfc-rbac) for authorization
* Events

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
     * @param mixed $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
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

### Using the RepositoryManager

The default `RepositoryManager` implementation is bundled with Doctrine, ZF2 EventManager and [zfc-rbac](https://github.com/ZF-Commons/zfc-rbac).

#### Setting up permissions

Not everyone should be allowed to commit, reject and accept revisions, right? Therefore, the `RepositoryManager` is able to handle permissions via [zfc-rbac](https://github.com/ZF-Commons/zfc-rbac)!

To set up permissions, you will need to add some config data to your [module.config.php](http://framework.zend.com/manual/2.3/en/modules/zend.module-manager.intro.html):

```php
return [
    // ...
    'versioning'       => [
        'permissions'  => [
            // Use the classname of the revision class
            // In the example above the namespace is missing, therefore the classname is only "Revision".
            // This could be also "MyModule\Entity\Revision"
            'Revision' => [
            
                // There are three actions which need authentication:
                
                // 'commit' gets checked when you call "commitRevision"
                'commit'   => 'revision.create',
                
                // 'checkout' gets checked when you call "checkoutRevision"
                'checkout' => 'revision.checkout',
                
                // 'reject' gets checked when you call "rejectRevision"
                'reject'   => 'revision.trash'
                
                // Name the permissions whatever you like. Just be aware that they are registered in zfc-rbac!
                // 'commit' gets checked when you call "commitRevision"
                // 'commit'   => 'mymodule.entity.revision.commit',
                // 'checkout' => 'mymodule.entity.revision.checkout',
                // 'reject'   => 'mymodule.entity.revision.trash'
            ]
        ]
    ]
    // ...
];
```

**Important:** The revision is always passed to [zfc-rbac](https://github.com/ZF-Commons/zfc-rbac) as a context object for usage with e.g. Assertions! 

#### Setting up the EventManager

Apart from checking permissions, the `VersioningManager` is also able to trigger ZF2 Events. Let's take an example:

... to be done

#### Create a new Revision and fill it with data!

```php
// Let's create a repository first
$repository = new Repository();

// Now we need the RepositoryManager
$repositoryManager = $serviceManager->get('Versioning\Manager\VersioningManager');

// Let's create our first revision!
$revision = $repositoryManager->commitRevision($repository, ['foo' => 'bar'], 'I added some stuff');

// And check it out (set as HEAD / current revision)
// We can also add a short message, why we checked out this revision!
$repositoryManager->checkoutRevision($revision, 'That\'s a nice reason, isn\'t it?');

// Now, let's make those changes persistent!
$repositoryManager->flush();
```

#### Trash a revision

Someone made a mistake? Just reject the revision!

```php
// Now we need the RepositoryManager
$repositoryManager = $serviceManager->get('Versioning\Manager\VersioningManager');

// Let's find the revision!
$revision = $repositoryManager->rejectRevision($repository, 5, 'Sorry but there are too many mistakes!');

// Now, let's make those changes persistent!
$repositoryManager->flush();
```

## To be done

There are a more things to come:

* A beautiful UI to manage your repositories
* A RESTful API
* Better docs
