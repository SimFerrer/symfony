<?php

namespace App\Controller\Api;

use App\DTO\BookFilter;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/book')]
class BookController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(BookService $bookService, PaginationService $paginationService, Request $request, BookRepository $bookRepository)
    {
        try {
            $filter = BookFilter::fromRequest($request->query->all());
            $page = $request->query->getInt('page');
            $books = $bookService->getAll($filter, $page, $paginationService, $bookRepository);
            return $this->json($books, 200, [], [
                'groups' => ['books.index']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Books not found'], 404);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(int $id, BookService $bookService, BookRepository $bookRepository)
    {
        try {
            $book = $bookService->getById($id, $bookRepository);

            return $this->json($book, 200, [], [
                'groups' => ['books.index', 'books.show']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/create', methods: ['POST'])]
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    public function create(Request $request, BookService $bookService)
    {
        try {
            // Appeler le service pour créer un livre
            $book = $bookService->create($request->getContent());

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
    #[IsGranted('ROLE_EDITION_DE_LIVRE')]
    public function edit(Request $request, BookService $bookService)
    {
        try {
            $book = $bookService->update($request->getContent());

            return $this->json($book, 200, [], [
                'groups' => ['books.edit']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }


    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    public function delete(int $id, BookService $bookService, BookRepository $bookRepository)
    {
        try {
            $bookService->delete($id, $bookRepository);

            return $this->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
