<?php

namespace App\Tests\Controller\Api;

use App\Tests\Util\JwtUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected JwtUtil $jwtUtil;
    protected EntityManagerInterface $entityManager;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->entityManager->beginTransaction();

        $this->jwtUtil = new JwtUtil(
            $container->get('lexik_jwt_authentication.jwt_manager'),
            $container->get('doctrine')->getRepository(\App\Entity\User::class)
        );
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        parent::tearDown();
    }

    protected function getJwtToken(int $userId): string
    {
        return $this->jwtUtil->generateJwtForUser($userId);
    }

    protected function assertJsonResponse(int $statusCode, string $responseContent): void
    {
        $this->assertResponseStatusCodeSame($statusCode);
        $this->assertJson($responseContent);
    }

    protected function assertSuccessMessage(string $message): void
    {
        $this->assertStringContainsString($message, $this->client->getResponse()->getContent());
    }
}
