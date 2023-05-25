<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use App\DataFixtures\SeasonFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
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
            for ($j = 0; $j < 5; $j++){
                $seasonReference = 'season_' . $i . '_' . $j;
                for ($k = 0; $k < 10; $k++) {
                    $episode = new Episode();
                    //Ce Faker va nous permettre d'alimenter l'instance de Season que l'on souhaite ajouter en base
                    $episode->setTitle($faker->sentence());
                    $episode->setNumber($faker->numberBetween($k + 1));
                    $episode->setSynopsis($faker->paragraphs(3, true));
                    //$randomSeasonNumber = $faker->numberBetween(1, 10);
                    //$episode->setSeason($this->getReference($seasonReference));

                    $episode->setSeason($this->getReference($seasonReference));

                    $manager->persist($episode);
                }
            }
        }

        $manager->flush();

    }

         public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          SeasonFixtures::class,
        ];
    }
}