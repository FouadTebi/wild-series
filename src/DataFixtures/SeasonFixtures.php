<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Season;
use App\DataFixtures\ProgramFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();

        /**
        * L'objet $faker que tu récupère est l'outil qui va te permettre 
        * de te générer toutes les données que tu souhaites
        */
        
        for($i = 0; $i < 20; $i++) {
            $programReference = 'program_' . $i;
            for ($j = 0; $j < 5; $j++){
                $season = new Season();
                //Ce Faker va nous permettre d'alimenter l'instance de Season que l'on souhaite ajouter en base
                $season->setNumber($faker->numberBetween($j + 1));
                $season->setYear($faker->year());
                $season->setDescription($faker->paragraphs(3, true));

                $season->setProgram($this->getReference($programReference));
                $this->addReference('season_' . $i .'_'. $j, $season);
                $manager->persist($season);
            }
        }

        $manager->flush();
    }
         public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          ProgramFixtures::class,
        ];
    }
}