<?php

namespace Athene2Test\VersioningTest\Filter;

use Athene2\Versioning\Filter\HasCurrentRevisionCollectionFilter;
use Athene2Test\VersioningTest\Asset\RepositoryFake;
use Athene2Test\VersioningTest\Asset\RevisionFake;

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
 