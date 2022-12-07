<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Repository\ProgramRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;
class ActorFixtures extends Fixture
{
    const CAT = [
        'category_Aventure',
        'category_Action',
        'category_Fantastique',
        'category_Animation',
        'category_Horreur',
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name());
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
        }
        $manager->flush();
    }
}
