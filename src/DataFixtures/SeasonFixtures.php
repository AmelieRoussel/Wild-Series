<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i=0; $i<20; $i++) {
            $season = new Season();
            $season->setNumber($faker->numberBetween(1, 15));
            $season->setYear($faker->year);
            $season->setDescription($faker->text(400));
            $season->setProgram($this->getReference('program_' . rand(0,5)));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}