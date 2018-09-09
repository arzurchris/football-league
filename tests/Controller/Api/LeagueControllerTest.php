<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 07/09/2018
 * Time: 18:33
 */

namespace App\Tests\Controller\Api;

use App\Entity\League;

class LeagueControllerTest extends AbstractControllerTest
{

    private $token;

    protected function setUp()
    {
        parent::setUp();

        $this->token = static::$kernel->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->encode(['username' => 'my_username']);

    }

    public function testDeleteLeague(): void
    {
        $response = $this->client->delete('/api/league/123456789', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertFalse($data['result']);

        $response = $this->client->get('/api/leagues', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        /** @var League $league */
        $league = reset($result);

        $response = $this->client->delete('/api/league/' . $league['id'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertTrue($data['result']);

        $response = $this->client->get('/api/league/' . $league['id'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertEquals($response->getStatusCode(), 404);
    }
}