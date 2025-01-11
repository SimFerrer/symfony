<?php

namespace App\Controller\Api;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/editor')]
class EditorController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(EditorRepository $editorRepository, PaginationService $paginationService, Request $request)
    {

        $queryBuilder = $editorRepository->createQueryBuilder('e');
        $editors = $paginationService->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->json($editors, 200, [], [
            'groups' => ['editor.index']
        ]);
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Editor $editor)
    {
        return $this->json($editor, 200, [], [
            'groups' => ['editor.index', 'editor.show']
        ]);
    }
}
