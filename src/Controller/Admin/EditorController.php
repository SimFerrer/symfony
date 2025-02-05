<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use App\Service\EditorService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor', methods: ['GET'])]
    public function index(Request $request, EditorService $editorService, PaginationService $paginationService, EditorRepository $editorRepository): Response
    {
        $editors = $editorService->getAll(null, $request->query->getInt('page', 1), $paginationService, $editorRepository);
        return $this->render('admin/editor/index.html.twig', [
            'controller_name' => 'EditorController',
            'editors' => $editors
        ]);
    }


    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_editor_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Editor $editor, Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($editor) {
            $this->denyAccessUnlessGranted('ROLE_EDITION_DE_LIVRE');
        }

        $editor ??= new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($editor);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_editor');
        }

        return $this->render('admin/editor/new.html.twig', [
            'controller_name' => 'EditorController',
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_admin_editor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'controller_name' => 'EditorController',
            'editor' => $editor,
        ]);
    }
}
