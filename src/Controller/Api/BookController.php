<?php

namespace App\Controller\Api;

use App\DTO\BookFilter;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/book')]
class BookController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(BookService $bookService, PaginationService $paginationService, Request $request)
    {
        try {
            $filter = BookFilter::fromRequest($request->query->all());
            $page = $request->query->getInt('page', 1);
            $books = $bookService->getBookAll($filter, $page, $paginationService);
            return $this->json($books, 200, [], [
                'groups' => ['books.index']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Books not found'], 404);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(int $id, BookService $bookService)
    {
        try {
            $book = $bookService->getBookById($id);

            return $this->json($book, 200, [], [
                'groups' => ['books.index', 'books.show']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request, BookService $bookService)
    {
        try {
            // Appeler le service pour créer un livre
            $book = $bookService->createBook($request->getContent());

            // Retourner la réponse
            return $this->json($book, 201, [], [
                'groups' => ['books.edit']
            ]);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/edit', methods: ['PUT'])]
    public function edit(Request $request, BookService $bookService)
    {
        try {
            $book = $bookService->updateBook($request->getContent());

            return $this->json($book, 200, [], [
                'groups' => ['books.edit']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }


    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(int $id, BookService $bookService)
    {
        try {
            $bookService->deleteBook($id);

            return $this->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
