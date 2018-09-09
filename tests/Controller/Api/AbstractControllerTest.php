<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 09/09/2018
 * Time: 12:26
 */

namespace App\Tests\Controller\Api;

use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client;

/**
 * Class AbstractControllerTest
 * @package App\Tests\Controller\Api
 */
abstract class AbstractControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    private static $staticClient;

    public static function setUpBeforeClass()
    {
        self::$staticClient = new Client([
            'base_uri'    => 'http://localhost:8000',
            'http_errors' => false,
        ]);
    }

    protected function setUp()
    {
        self::bootKernel();
        $this->client = self::$staticClient;

        // $this->purgeDatabase();

        // TODO load fixtures
        // self::runCommand('doctrine:fixtures:load --append');

        $this->createUser('my_username', 'my_password');
    }

    private function purgeDatabase(): void
    {
        $purger = new ORMPurger(self::$kernel->getContainer()->get('doctrine')->getManager());
        $purger->purge();
    }

    /**
     * @param string $username
     * @param string $plainPassword
     * @return User
     */
    protected function createUser(string $username, string $plainPassword): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($username . '@bar.com');

        $password = self::$kernel->getContainer()
            ->get('security.password_encoder')
            ->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        $em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function tearDown()
    {
        $em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('App:User')->findOneBy(['username' => 'my_username']);

        $em->remove($user);
        $em->flush();
    }
}