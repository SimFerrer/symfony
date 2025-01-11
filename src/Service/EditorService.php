<?php

namespace App\Service;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditorService
{
    private $entityManager;
    private $editorRepository;
    private $serializer;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EditorRepository $editorRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->editorRepository = $editorRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function getEditorAll($page, PaginationService $paginationService)
    {
        $queryBuilder = $this->editorRepository->createQueryBuilder('e');
        $editors = $paginationService->paginate(
            $queryBuilder,
            $page,
            20
        );
        return $editors;
    }

    public function getEditorById(int $id): ?Editor
    {
        $editor = $this->editorRepository->find($id);
        if (!$editor) {
            throw new \Exception('Editor not found');
        }

        return $editor;
    }


    public function deleteEditor(int $id): void
    {
        $editor = $this->editorRepository->find($id);

        if (!$editor) {
            throw new \Exception('Editor not found');
        }
        $this->entityManager->remove($editor);
        $this->entityManager->flush();
    }

    public function createEditor(string $data): Editor
    {
        $editor = $this->prepareEditor($data);

        $this->entityManager->persist($editor);
        $this->entityManager->flush();

        return $editor;
    }

    public function updateEditor(string $data): Editor
    {
        $editor = $this->prepareEditor($data, true);

        // Sauvegarde en base de données
        $this->entityManager->flush();

        return $editor;
    }


    private function prepareEditor(string $data, bool $edit = false): Editor
    {
        $decodedData = json_decode($data, true);

        if (!$decodedData) {
            throw new \Exception('Invalid JSON');
        }

        ($edit) ? $editor = $this->editorRepository->find($decodedData['id']) : $editor = new Editor();

        $this->serializer->deserialize($data, Editor::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $editor,
        ]);
        if (!$editor) {
            throw new \Exception('Editor wrong format');
        }
        $errors = $this->validator->validate($editor);
        if (count($errors) > 0) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        return $editor;
    }
}
