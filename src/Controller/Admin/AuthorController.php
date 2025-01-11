<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Service\AuthorService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    #[Route('', name: 'app_admin_author')]
    public function index(Request $request, AuthorService $authorService, PaginationService $paginationService): Response
    {
        try {
            $dates = [];
            if ($request->query->has('start')) {
                $dates['start'] = $request->query->get('start');
            }
            if ($request->query->has('end')) {
                $dates['end'] = $request->query->get('end');
            }
            $authors = $authorService->getAuthorAll($request->query->getInt('page', 1), $paginationService, $dates);
            return $this->render('admin/author/index.html.twig', [
                'controller_name' => 'AuthorController',
                'authors' => $authors,
            ]);
        } catch (\Exception $e) {
            throw $this->createNotFoundException('An error occurred while fetching authors.', $e);
        }
    }

    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Author $author, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($author) {
            if (!$this->isGranted('ROLE_EDITION_DE_LIVRE')) {
                throw $this->createNotFoundException('Support Group does not exist');
            }
        }
        $author ??= new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
        }

        return $this->render('admin/author/new.html.twig', [
            'controller_name' => 'AuthorController',
            'form' => $form
        ]);
    }
}
