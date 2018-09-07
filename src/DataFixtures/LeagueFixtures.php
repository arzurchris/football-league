<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 07/09/2018
 * Time: 16:34
 */

namespace App\DataFixtures;

use App\Entity\League;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LeagueFixtures extends Fixture
{

    public const LEAGUE_1_REFERENCE = 'league-1';
    public const LEAGUE_2_REFERENCE = 'league-2';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $league1 = new League();
        $league1->setName('league 1');
        $manager->persist($league1);
        $manager->flush();

        $this->addReference(self::LEAGUE_1_REFERENCE, $league1);

        $league2 = new League();
        $league2->setName('league 2');
        $manager->persist($league2);
        $manager->flush();

        $this->addReference(self::LEAGUE_2_REFERENCE, $league2);

    }
}