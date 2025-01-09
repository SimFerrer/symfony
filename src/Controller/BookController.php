<?php

namespace App\Controller;

use App\DTO\BookFilter;
use App\Entity\Book;
use App\Repository\BookRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $repository): Response
    {

        $filter = BookFilter::fromRequest($request->query->all());
        $queryBuilder = $repository->findFilteredBooks($filter);
        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($queryBuilder),
            $request->query->get('page', 1),
            20
        );

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
