<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 07/09/2018
 * Time: 16:35
 */

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class TeamFixtures
 * @package App\DataFixtures
 */
class TeamFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // create 20 teams
        for ($i = 0; $i < 20; $i++) {
            $team = new Team();
            $team->setName('team ' . $i);
            $arrayReference = [LeagueFixtures::LEAGUE_1_REFERENCE, LeagueFixtures::LEAGUE_2_REFERENCE];
            $reference = $arrayReference[array_rand($arrayReference)];
            $team->setLeague($this->getReference($reference));
            $manager->persist($team);
        }

        $manager->flush();

    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return array(
            LeagueFixtures::class,
        );
    }
}