<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{

    private EntityManagerInterface $entityManager;

    


    public function testIndex(): void
    {
        $client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        // Créer un utilisateur et le persister
        $user = $this->createPersistedUser();
        $client->loginUser($user);

        $client->request('GET', '/admin/book');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste de livres');
    }


    private function createPersistedUser(): User
    {
        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setUsername('admin');
        $user->setFirstname('prénom');
        $user->setLastname('nom de famille');
        $user->setRoles(['ROLE_AJOUT_DE_LIVRE', 'ROLE_EDITION_DE_LIVRE']);
        $user->setPassword('faezfaezgfafazfz1!'); // Dans un test, vous n'avez pas besoin de hacher le mot de passe

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    protected function tearDown(): void
{
    // Supprimer les utilisateurs persistés pour éviter les conflits
    //$this->entityManager = static::getContainer()->get('doctrine')->getManager();
    $userRepository = $this->entityManager->getRepository(User::class);
    $users = $userRepository->findBy(['email' => 'test@test.fr']);
    foreach ($users as $user) {
        $this->entityManager->remove($user);
    }
    $this->entityManager->flush();

    parent::tearDown();
}

}
