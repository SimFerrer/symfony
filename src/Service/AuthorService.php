<?php

namespace App\Service;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\Abstract\AbstractEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorService extends AbstractEntityService
{


    private BookRepository $bookRepository;
    private AuthorRepository $authorRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        AuthorRepository $authorRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        parent::__construct($entityManager, $serializer, $validator);
        $this->bookRepository = $bookRepository;
        $this->authorRepository = $authorRepository;
    }


    protected function customizeQueryBuilder($repository, $filter): ?QueryBuilder
    {
        return $this->authorRepository->findByDateOfBirth($filter);
    }



    protected function prepare(string $data, bool $edit = false): Author
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
            throw new \Exception("Incorrect data");
        }

        return $author;
    }
}
