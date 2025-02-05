<?php

namespace App\Controller\Api;

use App\Repository\EditorRepository;
use App\Service\EditorService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/editor')]
class EditorController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(EditorService $editorService, PaginationService $paginationService, Request $request, EditorRepository $editorRepository)
    {
        try {
            $page = $request->query->getInt('page');
            $editors = $editorService->getAll(null, $page, $paginationService, $editorRepository);
            return $this->json($editors, 200, [], [
                'groups' => ['editor.index']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Editors not found'], 404);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(int $id, EditorService $editorService, EditorRepository $editorRepository)
    {
        try {
            $editor = $editorService->getById($id, $editorRepository);
            return $this->json($editor, 200, [], [
                'groups' => ['editor.index', 'editor.show']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Author not found'], 404);
        }
    }

    #[Route('/create', methods: ['POST'])]
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    public function create(Request $request, EditorService $editorService)
    {
        try {
            // Appeler le service pour créer un livre
            $editor = $editorService->create($request->getContent());

            // Retourner la réponse
            return $this->json($editor, 201, [], [
                'groups' => ['editor.edit']
            ]);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/edit', methods: ['PUT'])]
    #[IsGranted('ROLE_EDITION_DE_LIVRE')]
    public function edit(Request $request, EditorService $editorService)
    {
        try {
            $editor = $editorService->update($request->getContent());
            return $this->json($editor, 200, [], [
                'groups' => ['editor.edit']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    public function delete(int $id, EditorService $editorService, EditorRepository $editorRepository)
    {
        try {
            $editorService->delete($id, $editorRepository);

            return $this->json(['message' => 'Editor deleted successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
