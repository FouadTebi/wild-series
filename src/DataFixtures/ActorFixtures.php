<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Actor;
use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ProgramFixtures;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $actor = new Actor();
            $actor->setFirstname($faker->firstName());
            $actor->setLastname($faker->lastName());
            $birthDate = $faker->dateTimeBetween('-65 years', '-16 years');
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
                $program->setSynopsis($faker->sentence);
                $program->setYear($faker->year());
                $program->setCountry($faker->sentence);
                $program->setSlug($faker->slug());

                // Associez l'acteur au programme
                $actor->addProgram($program);

                // Appel explicite à persist() pour le programme
                $manager->persist($program);
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
           CategoryFixtures::class,
           ProgramFixtures::class,
        ];
    }
}