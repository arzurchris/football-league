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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TeamControllerTest
 * @package App\Tests\Controller\Api
 */
class TeamControllerTest extends WebTestCase
{
    public function testGetTeams(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/teams');
        $response = $client->getResponse();

        $this->assertEquals($response->getStatusCode(), 200);
        $data = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertCount(20, $data);
        $this->assertContainsOnlyInstancesOf(Team::class, $data);
    }

    public function testPostTeam(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/leagues');
        $oResponse = $client->getResponse();
        $result = json_decode($oResponse->getContent(), true);
        /** @var League $league */
        $league = reset($result);

        $data = ['name' => 'PSG', 'league' => $league];

        $client->request('POST', '/api/teams', ['body' => json_encode($data)]);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('result', $data);
        $this->assertTrue($data['result']);
        $this->assertArrayHasKey('id', $data);
        $this->assertInternalType('integer', $data['id']);

    }

    public function testPutTeam()
    {
        $client = static::createClient();

        $client->request('GET', '/api/leagues');
        $oResponse = $client->getResponse();
        $result = json_decode($oResponse->getContent(), true);
        /** @var League $league */
        $league = reset($result);

        $client->request('GET', '/api/teams');
        $oResponse = $client->getResponse();
        $result = json_decode($oResponse->getContent(), true);
        /** @var Team $team */
        $team = reset($result);

        $data = ['name' => 'SRFC', 'league' => $league];

        $client->request('PUT', '/api/team/' . $team->getId(), [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], ['body' => json_encode($data)]);
        $oResponse = $client->getResponse();
        $this->assertEquals(200, $oResponse->getStatusCode());
        $data = json_decode($oResponse->getContent(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertTrue($data['result']);
    }
}