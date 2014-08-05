<?php

namespace VersioningTest\Filter;

use Versioning\Filter\HasCurrentRevisionCollectionFilter;
use VersioningTest\Asset\RepositoryFake;
use VersioningTest\Asset\RevisionFake;

class HasCurrentRevisionCollectionFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $repository = new RepositoryFake();
        $filter     = new HasCurrentRevisionCollectionFilter();
        $collection = [
            $repository,
            new RepositoryFake(),
            new RepositoryFake()
        ];

        $repository->setCurrentRevision(new RevisionFake());
        $this->assertSame([$repository], $filter->filter($collection));
    }
}
 