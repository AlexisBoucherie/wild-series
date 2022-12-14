<?php

namespace App\DataFixtures;

use App\Entity\Program;
use ContainerRDu5FCj\getSluggerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const TITLE = [
        'Star Wars',
        'Lord of the Rings',
        'Back to the Future',
        'Jurassic Park',
        'The Expanse'
    ];
    const SYNOPSIS = [
        'Les méchants Siths veulent gouverner la galaxie. C\'est sans compter sur les gentils Jedis.',
        'Sauron essaye de revenir à la vie pour dominer la Terre du Milieu. La communauté de l\'anneau va l\'en empêcher',
        'On était en l\'an 2000, puis on est allé dans le passé, puis à nouveau dans le futur !',
        'Un savant fou fait revenir des dinos à la vie, et ça va merder, forcément.',
        'Mars et la Terre luttent pour la domination de l\'espace, mais une histoire de protomolécule alien va bouleverser l\'ordre des choses',
    ];
    const CAT = [
        'category_Aventure',
        'category_Action',
        'category_Fantastique',
        'category_Animation',
        'category_Horreur',
    ];

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $program = new Program();
            $program->setTitle(self::TITLE[$i]);
            $program->setSynopsis(self::SYNOPSIS[$i]);
            $program->setCategory($this->getReference(self::CAT[$i]));
            $program->setOwner($this->getReference('user_'.$faker->numberBetween(1, 2)));
            for ($j = 0;$j < 4; $j++) {
                $program->addActor($this->getReference('actor_' . $faker->numberBetween(0, 49)));
            }
            $program->setSlug($this->slugger->slug($program->getTitle()));
            $this->addReference('program_' . $i, $program);
            $manager->persist($program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }


}

