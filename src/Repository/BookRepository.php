<?php

namespace App\Repository;

use App\DTO\BookFilter;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }


    public function findFilteredBooks(BookFilter $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->addSelect('a');

        if ($filter && $filter->value) {
            $qb->andWhere('b.title LIKE :search OR a.name LIKE :search')
                ->setParameter('search', '%' . $filter->value . '%');
        }

        return $qb;
    }
}
