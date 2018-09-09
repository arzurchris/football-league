<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 09/09/2018
 * Time: 12:48
 */

namespace App\Tests\Controller\Api;

class TokenControllerTest extends AbstractControllerTest
{

    public function testPOSTCreateToken(): void
    {
        $username = 'my_username';
        $password = 'my_password';

        $response = $this->client->post('/api/tokens', [
            'auth' => [$username, $password]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('token', $data);

    }

    public function testPOSTTokenInvalidCredentials(): void
    {
        $username = 'my_username';
        $wrongPassword = 'wrong_password';

        $response = $this->client->post('/api/tokens', [
            'auth' => [$username, $wrongPassword]
        ]);

        $this->assertEquals(401, $response->getStatusCode());

    }
}