<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 07/09/2018
 * Time: 17:43
 */

namespace App\Tests\Controller\Api;

use App\Entity\League;
use App\Entity\Team;

/**
 * Class TeamControllerTest
 * @package App\Tests\Controller\Api
 */
class TeamControllerTest extends AbstractControllerTest
{

    private $token;

    protected function setUp()
    {
        parent::setUp();

        $this->token = static::$kernel->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->encode(['username' => 'my_username']);

    }

    public function testBadToken(): void
    {
        $response = $this->client->post('/api/teams', [
            'body'    => '[]',
            'headers' => [
                'Authorization' => 'Bearer WRONG'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testGetTeams(): void
    {

        $response = $this->client->get('/api/teams', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals($response->getStatusCode(), 200);
        $data = json_decode($response->getBody());

        $this->assertInternalType('array', $data);
        // $this->assertCount(20, $data);

    }

    public function testPostTeam(): void
    {
        $response = $this->client->post('/api/teams', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertFalse($data['result']);
        $this->assertEquals('Parameter not defined : name', $data['message']);

        $body = ['name' => 'PSG'];
        $response = $this->client->post('/api/teams', [
            'body'    => json_encode($body),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertFalse($data['result']);
        $this->assertEquals('Parameter not defined : leagueId', $data['message']);

        $body = ['name' => 'PSG', 'leagueId' => 12345678];
        $response = $this->client->post('/api/teams', [
            'body'    => json_encode($body),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertFalse($data['result']);
        $this->assertEquals('League does not exist with id:' . $body['leagueId'], $data['message']);

        $response = $this->client->get('/api/leagues', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $result = json_decode($response->getBody(), true);
        /** @var League $league */
        $league = reset($result);

        $body = ['name' => 'PSG', 'leagueId' => $league['id']];

        $response = $this->client->post('/api/teams', [
            'body'    => json_encode($body),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('result', $data);
        $this->assertTrue($data['result']);
        $this->assertArrayHasKey('id', $data);
        $this->assertInternalType('integer', $data['id']);

    }

    public function testMethodNotAllowed(): void
    {

        $response = $this->client->post('/api/team/12345678', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('Method Not Allowed', $response->getReasonPhrase());
    }

    public function testPutTeam(): void
    {
        $response = $this->client->put('/api/team/12345678', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertFalse($data['result']);
        $this->assertEquals('Team does not exist with id:12345678', $data['message']);

        $response = $this->client->get('/api/leagues', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $result = json_decode($response->getBody(), true);
        /** @var League $league */
        $league = reset($result);

        $response = $this->client->get('/api/teams', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $result = json_decode($response->getBody(), true);
        /** @var Team $team */
        $team = reset($result);

        $body = ['name' => 'SRFC', 'strip' => 'puma', 'leagueId' => $league['id']];

        $response = $this->client->put('/api/team/' . $team['id'], [
            'body'    => json_encode($body),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertTrue($data['result']);
    }


}