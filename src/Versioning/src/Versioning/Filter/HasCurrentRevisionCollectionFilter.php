<?php

namespace Versioning\Filter;

use Doctrine\Common\Collections\Collection;
use Versioning\Entity\RepositoryInterface;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

/**
 * Class HasCurrentRevisionCollectionFilter
 *
 * @package Versioning\Filter
 * @author  Aeneas Rekkas
 */
class HasCurrentRevisionCollectionFilter implements FilterInterface
{
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
        if (!$value instanceof Collection) {
            throw new Exception\RuntimeException(sprintf(
                'Expected Collection but got %s',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->filter(
            function (RepositoryInterface $repository) {
                return $repository->hasCurrentRevision();
            }
        );
    }
}
