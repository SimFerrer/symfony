<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;

class AuthorControllerTest extends AbstractControllerTest
{
    public function testGetAllAuthors(): void
    {
        $jwt = $this->jwtUtil->generateJwtForUser(15);
        $this->client->request('GET', '/api/author', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');

        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testGetAuthorById(): void
    {
        $jwt = $this->jwtUtil->generateJwtForUser(15);

        $this->client->request('GET', '/api/author/152', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');
        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testCreateAuthor(): void
    {
        $jwt = $this->getJwtToken(15);

        $data = [
            'name' => 'Test Author',
            'nationality' => 'France',
            'dateOfBirth' => '1980-01-01'
        ];

        $this->client->request('POST', '/api/author/create', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode($data));

        $this->assertJsonResponse(Response::HTTP_CREATED, $this->client->getResponse()->getContent());
    }

    public function testUpdateAuthor(): void
    {
        $jwt = $this->getJwtToken(15);
        $data = [
            'id' => 152,
            'name' => 'Test Author',
            'nationality' => 'France',
            'dateOfBirth' => '1980-01-01'
        ];

        $this->client->request('PUT', '/api/author/edit', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], json_encode($data));

        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testDeleteAuthor(): void
    {

        $jwt = $this->jwtUtil->generateJwtForUser(15);

        $this->client->request('DELETE', '/api/author/152', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('Author deleted successfully', $this->client->getResponse()->getContent());
    }
}
