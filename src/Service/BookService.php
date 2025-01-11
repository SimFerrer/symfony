<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\EditorRepository;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService
{
    private $entityManager;
    private $bookRepository;
    private $editorRepository;
    private $authorRepository;
    private $userRepository;
    private $serializer;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        EditorRepository $editorRepository,
        AuthorRepository $authorRepository,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }


    public function getBookAll($filter, $page, PaginationService $paginationService)
    {
        $queryBuilder = $this->bookRepository->findFilteredBooks($filter);
        $books = $paginationService->paginate(
            $queryBuilder,
            $page,
            20
        );
        return $books;
    }
    public function getBookById(int $id): ?Book
    {
        $book = $this->bookRepository->find($id);
        if (!$book) {
            throw new \Exception('Book not found');
        }

        return $book;
    }
    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            throw new \Exception('Book not found');
        }
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    public function createBook(string $data): Book
    {
        $book = $this->prepareBook($data);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }

    public function updateBook(string $data): Book
    {
        $book = $this->prepareBook($data, true);

        // Sauvegarde en base de données
        $this->entityManager->flush();

        return $book;
    }


    private function prepareBook(string $data, bool $edit = false): Book
    {
        $decodedData = json_decode($data, true);

        if (!$decodedData) {
            throw new \Exception('Invalid JSON');
        }

        ($edit) ? $book = $this->bookRepository->find($decodedData['id']) : $book = new Book();

        $this->serializer->deserialize($data, Book::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $book,
        ]);
        if (!$book) {
            throw new \Exception('Book wrong format');
        }
        $book->setEditedAt(new \DateTimeImmutable());
        $editorId = $decodedData['editor_id'] ?? null;
        $editor = $this->editorRepository->find($editorId);
        if (!$editor) {
            throw new \Exception('Editor not found');
        }
        $book->setEditor($editor);


        $authorIds = $decodedData['author_ids'] ?? [];
        $authors = $this->authorRepository->findBy(['id' => $authorIds]);
        if (count($authors) !== count($authorIds)) {
            throw new \Exception("One or more authors not found");
        }
        foreach ($authors as $author) {
            $book->addAuthor($author);
        }

        $user = $this->userRepository->find(12);
        $book->setCreatedBy($user);

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        return $book;
    }
}
