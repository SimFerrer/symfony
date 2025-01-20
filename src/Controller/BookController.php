<?php

namespace App\Controller;

use App\DTO\BookFilter;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(Request $request, BookService $bookService, PaginationService $paginationService, BookRepository $bookRepository): Response
    {

        $filter = BookFilter::fromRequest($request->query->all());
        $books = $bookService->getAll($filter,  $request->query->getInt('page', 1), $paginationService, $bookRepository);


        return $this->render('book/index.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
