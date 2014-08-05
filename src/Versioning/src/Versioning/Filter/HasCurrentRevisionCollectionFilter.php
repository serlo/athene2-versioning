<?php

namespace Versioning\Filter;

use Versioning\Entity\RepositoryInterface;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;
use Zend\Stdlib\Guard\ArrayOrTraversableGuardTrait;

/**
 * Class HasCurrentRevisionCollectionFilter
 *
 * @package Versioning\Filter
 * @author  Aeneas Rekkas
 */
class HasCurrentRevisionCollectionFilter implements FilterInterface
{
    use ArrayOrTraversableGuardTrait;

    /**
     * Filters out all RepositoryInterfaces without a checked out revision
     * contained in a RepositoryInterface Collection
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $this->guardForArrayOrTraversable($value);
        return array_filter($value, function (RepositoryInterface $repository) {
            return $repository->hasCurrentRevision();
        });
    }
}
