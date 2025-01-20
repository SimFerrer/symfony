<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;

class EditorControllerTest extends AbstractControllerTest
{
    public function testGetAllEditors(): void
    {
        $jwt = $this->jwtUtil->generateJwtForUser(15);
        $this->client->request('GET', '/api/editor', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');

        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testGetEditorById(): void
    {
        $jwt = $this->jwtUtil->generateJwtForUser(15);

        $this->client->request('GET', '/api/editor/43', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');
        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testCreateEditor(): void
    {
        $jwt = $this->getJwtToken(15);

        $data = [
            'name' => 'Test Editor'
        ];

        $this->client->request('POST', '/api/editor/create', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], json_encode($data));

        $this->assertJsonResponse(Response::HTTP_CREATED, $this->client->getResponse()->getContent());
    }

    public function testUpdateEditor(): void
    {
        $jwt = $this->getJwtToken(15);
        $data = [
            'id' => 43,
            'name' => 'Test Editor'
        ];

        $this->client->request('PUT', '/api/editor/edit', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], json_encode($data));

        $this->assertJsonResponse(Response::HTTP_OK, $this->client->getResponse()->getContent());
    }

    public function testDeleteEditor(): void
    {

        $jwt = $this->jwtUtil->generateJwtForUser(15);

        $this->client->request('DELETE', '/api/editor/43', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ], '');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('Editor deleted successfully', $this->client->getResponse()->getContent());
    }
}
