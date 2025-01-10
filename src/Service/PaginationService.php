<?php

namespace App\Service;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Doctrine\ORM\QueryBuilder;

class PaginationService
{
    public function paginate(QueryBuilder $queryBuilder, int $page = 1, int $maxPerPage = 20)
    {
        $adapter = new QueryAdapter($queryBuilder);
        $pagerFanta = new Pagerfanta($adapter);

        $pagerFanta->setCurrentPage($page);
        $pagerFanta->setMaxPerPage($maxPerPage);

        return $pagerFanta;
    }
}
