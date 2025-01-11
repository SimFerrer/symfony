<?php

namespace App\Controller\Api;

use App\Service\EditorService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/editor')]
class EditorController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(EditorService $editorService, PaginationService $paginationService, Request $request)
    {
        try {
            $editors = $editorService->getEditorAll($request->query->getInt('page', 1), $paginationService);
            return $this->json($editors, 200, [], [
                'groups' => ['editor.index']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Editors not found'], 404);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(int $id, EditorService $editorService)
    {
        try {
            $editor = $editorService->getEditorById($id);
            return $this->json($editor, 200, [], [
                'groups' => ['editor.index', 'editor.show']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Author not found'], 404);
        }
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request, EditorService $editorService)
    {
        try {
            // Appeler le service pour créer un livre
            $editor = $editorService->createEditor($request->getContent());

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
    public function edit(Request $request, EditorService $editorService)
    {
        try {
            $editor = $editorService->updateEditor($request->getContent());
            return $this->json($editor, 200, [], [
                'groups' => ['editor.edit']
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(int $id, EditorService $editorService)
    {
        try {
            $editorService->deleteEditor($id);

            return $this->json(['message' => 'Editor deleted successfully'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
