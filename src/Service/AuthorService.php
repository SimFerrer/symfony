<?php

namespace App\Service;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorService
{

    private $entityManager;
    private $bookRepository;
    private $authorRepository;
    private $serializer;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        AuthorRepository $authorRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        $this->authorRepository = $authorRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }


    public function getAuthorAll($page, PaginationService $paginationService)
    {
        $queryBuilder = $this->authorRepository->createQueryBuilder('a');
        $authors = $paginationService->paginate(
            $queryBuilder,
            $page,
            10
        );
        return $authors;
    }

    public function getAuthorById(int $id): ?Author
    {
        $author = $this->authorRepository->find($id);
        if (!$author) {
            throw new \Exception('Author not found');
        }
        return $author;
    }

    public function deleteAuthor(int $id): void
    {
        $author = $this->authorRepository->find($id);

        if (!$author) {
            throw new \Exception('Author not found');
        }
        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }

    public function createAuthor(string $data): Author
    {
        $author = $this->prepareAuthor($data);
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

    public function updateAuthor(string $data): Author
    {
        $book = $this->prepareAuthor($data, true);

        // Sauvegarde en base de données
        $this->entityManager->flush();

        return $book;
    }

    private function prepareAuthor(string $data, bool $edit = false): Author
    {
        $decodedData = json_decode($data, true);
        if (!$decodedData) {
            throw new \Exception('Invalid JSON');
        }
        ($edit) ? $author = $this->authorRepository->find($decodedData['id']) : $author = new Author();

        $this->serializer->deserialize(
            $data,
            Author::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $author],
        );
        if (!$author) {
            throw new \Exception('Author wrong format');
        }

        $booksIds = $decodedData['book_ids'] ?? [];
        $books = $this->bookRepository->findBy(['id' => $booksIds]);
        if (count($books) !== count($booksIds)) {
            throw new \Exception("One or more books not found");
        }

        foreach ($books as $book) {
            $author->addBook($book);
        }
        $errors = $this->validator->validate($author);
        if (count($errors) > 0) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        return $author;
    }
}
