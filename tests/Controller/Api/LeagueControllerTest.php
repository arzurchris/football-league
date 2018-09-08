<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 07/09/2018
 * Time: 18:33
 */

namespace App\Tests\Controller\Api;

use App\Entity\League;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeagueControllerTest extends WebTestCase
{

    public function testDeleteLeague(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/leagues');
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        /** @var League $league */
        $league = reset($result);
        
        $client->request('DELETE', '/api/league/'. $league['id']);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('result', $data);
        $this->assertTrue($data['result']);

        $client->request('GET', '/api/league/' . $league['id']);
        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), 404);
    }
}