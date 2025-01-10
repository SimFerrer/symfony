<?php

namespace App\Controller\Api;

use App\DTO\BookFilter;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/book')]
class BookController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(BookRepository $bookRepository, PaginationService $paginationService, Request $request)
    {
        $filter = BookFilter::fromRequest($request->query->all());
        $queryBuilder = $bookRepository->findFilteredBooks($filter);
        $books = $paginationService->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );
        return $this->json($books, 200, [], [
            'groups' => ['books.index']
        ]);
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Book $book)
    {
        return $this->json($book, 200, [], [
            'groups' => ['books.index', 'books.show']
        ]);
    }
}
