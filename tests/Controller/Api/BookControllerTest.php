<?php

namespace App\Tests\Controller\Api;

use App\Enum\BookStatus;
use Symfony\Component\HttpFoundation\Response;

class BookControllerTest extends AbstractControllerTest
{

    public function testGetAllBooks(): void
    {
        $this->client->request('GET', '/api/book');
        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testGetBookById(): void
    {
        $this->client->request('GET', '/api/book/202');

        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testCreateBook(): void
    {
        $jwt = $this->jwtUtil->generateJwtForUser(15);

        $data = [
            'title' => 'Test Book Php',
            'isbn' => '9784024621205',
            'cover' => 'https://via.placeholder.com/330x500.png/00bb00?text=couverture+deserunt',
            'plot' => 'Animi nemo odit et dolorem amet consequatur voluptas. Rerum excepturi aspernatur sit sint quo dolorem tempore. Doloribus consequatur odit inventore. Amet aut impedit qui officia.',
            'page_number' => '231',
            'status' => BookStatus::Available->value,
            'editor_id' => 43,
            'author_ids' => [103, 104]
        ];

        $this->client->request('POST', '/api/book/create', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], json_encode($data));
        $this->assertJsonResponse(Response::HTTP_CREATED, $this->client->getResponse()->getContent());
    }

    public function testUpdateBook(): void
    {

        $jwt = $this->jwtUtil->generateJwtForUser(15);
        $data = [
            'id' => 202,
            'title' => 'Updated Book',
            'isbn' => '9784024621205',
            'cover' => 'https://via.placeholder.com/330x500.png/00bb00?text=couverture+deserunt',
            'plot' => 'Animi nemo odit et dolorem amet consequatur voluptas. Rerum excepturi aspernatur sit sint quo dolorem tempore. Doloribus consequatur odit inventore. Amet aut impedit qui officia.',
            'page_number' => '231',
            'status' => BookStatus::Available->value,
            'editor_id' => 43,
            'author_ids' => [103, 104]
        ];

        $this->client->request('PUT', '/api/book/edit', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], json_encode($data));

        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testDeleteBook(): void
    {

        $jwt = $this->jwtUtil->generateJwtForUser(15);

        $this->client->request('DELETE', '/api/book/299', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('Book deleted successfully', $this->client->getResponse()->getContent());
    }
}
