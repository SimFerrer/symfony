<?php

namespace App\Service\Abstract;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractEntityService
{
    protected EntityManagerInterface $entityManager;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }


    protected function customizeQueryBuilder($repository,  $filter): ?QueryBuilder
    {
        return null;
    }

    protected function getDefaultSort(): array
    {
        return [];
    }

    protected function getPaginationLimit(): int
    {
        return 20;
    }

    abstract protected function prepare(string $data, bool $edit = false): object;

    public function getAll($filter, $page, $paginationService, $repository)
    {
        $queryBuilder = $this->customizeQueryBuilder($repository, $filter);

        if ($page && $queryBuilder) {
            return $paginationService->paginate($queryBuilder, $page, $this->getPaginationLimit());
        }

        $defaultSort = $this->getDefaultSort();
        $allItems = $repository->findBy([], $defaultSort);

        return [
            'items' => $allItems,
            'pagination' => [
                'currentPage' => 1,
                'totalItems' => count($allItems),
                'itemsPerPage' => count($allItems),
                'totalPages' => 1,
            ],
        ];
    }

    public function getById(int $id, $repository): object
    {
        $item = $repository->find($id);
        if (!$item) {
            throw new \Exception('Entity not found');
        }
        return $item;
    }

    public function delete(int $id, $repository): void
    {
        $item = $repository->find($id);

        if (!$item) {
            throw new \Exception('Entity not found');
        }
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    public function create(string $data): object
    {
        $entity = $this->prepare($data);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function update(string $data): object
    {
        $entity = $this->prepare($data, true);
        $this->entityManager->flush();

        return $entity;
    }
}
