<?php

namespace App\Service;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use App\Service\Abstract\AbstractEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditorService extends AbstractEntityService
{

    private EditorRepository $editorRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EditorRepository $editorRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        parent::__construct($entityManager, $serializer, $validator);
        $this->editorRepository = $editorRepository;
    }

    protected function customizeQueryBuilder($repository, $filter): ?QueryBuilder
    {
        return $this->editorRepository->createQueryBuilder('e');
    }

    protected function prepare(string $data, bool $edit = false): Editor
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
            throw new \Exception("Incorrect data");
        }

        return $editor;
    }
}
