<?php

namespace App\Controller\Api;

use App\Repository\AuthorRepository;
use App\Service\AuthorService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/author')]
class AuthorController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(AuthorService $authorService, PaginationService $paginationService, Request $request, AuthorRepository $authorRepository)
    {

        try {
            $dates = [];
            if ($request->query->has('start')) {
                $dates['start'] = $request->query->get('start');
            }
            if ($request->query->has('end')) {
                $dates['end'] = $request->query->get('end');
            }
            $page = $request->query->getInt('page');
            $authors = $authorService->getAll($dates, $page, $paginationService, $authorRepository);
            return $this->json($authors, 200, [], [
                'groups' => ['author.index']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(int $id, AuthorService $authorService, AuthorRepository $authorRepository)
    {
        try {
            $author = $authorService->getById($id, $authorRepository);
            return $this->json($author, 200, [], [
                'groups' => ['author.index', 'author.show']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Author not found'], 404);
        }
    }

    #[Route('/create', methods: ['POST'])]
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    public function create(Request $request, AuthorService $authorService)
    {
        try {
            $author = $authorService->create($request->getContent());

            return $this->json($author, 201, [], [
                'groups' => ['authors.edit']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/edit', methods: ['PUT'])]
    #[IsGranted('ROLE_EDITION_DE_LIVRE')]
    public function edit(Request $request, AuthorService $authorService)
    {
        try {
            $author = $authorService->update($request->getContent());
            return $this->json($author, 200, [], [
                'groups' => ['authors.edit']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    public function delete(int $id, AuthorService $authorService, AuthorRepository $authorRepository)
    {
        try {
            $authorService->delete($id, $authorRepository);

            return $this->json(['message' => 'Author deleted successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
