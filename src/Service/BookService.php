<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\EditorRepository;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Service\Abstract\AbstractEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService extends AbstractEntityService
{
    private BookRepository $bookRepository;
    private EditorRepository $editorRepository;
    private AuthorRepository $authorRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        BookRepository $bookRepository,
        EditorRepository $editorRepository,
        AuthorRepository $authorRepository,
        UserRepository $userRepository
    ) {
        parent::__construct($entityManager, $serializer, $validator);
        $this->bookRepository = $bookRepository;
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
        $this->userRepository = $userRepository;
    }

    protected function customizeQueryBuilder($repository, $filter): ?QueryBuilder
    {
        return $this->bookRepository->findFilteredBooks($filter);
    }

    protected function prepare(string $data, bool $edit = false): Book
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

        $user = $this->userRepository->find(15);
        $book->setCreatedBy($user);

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            throw new \Exception("Incorrect data");
        }
        return $book;
    }
}
