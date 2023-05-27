<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Actor;
use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $actor = new Actor();
            $actor->setFirstname($faker->firstname());
            $actor->setLastname($faker->lastname());
            $birthDate = $faker->dateTimeBetween('-60 years', '-18 years');
            $actor->setBirthDate($birthDate);
            $manager->persist($actor);

            $programs = $manager->getRepository(Program::class)->findAll();
            $randomPrograms = $faker->randomElements($programs, 3);

            foreach ($randomPrograms as $program) {
                $actor->addProgram($program);
            }

            for ($j = 0; $j < 3; $j++) {
                $program = new Program();
                $program->setTitle($faker->sentence);

                // Associez l'acteur au programme
                $actor->addProgram($program);
            }

            // Persistez l'acteur
            $manager->persist($actor);
        }

        // Flush pour enregistrer les données dans la base de données
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
           ProgramFixtures::class,
        ];
    }
}
