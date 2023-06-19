<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Program;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {   
        $faker = Factory::create();
        $admin = $this->getReference('admin');
        $contributor = $this->getReference('contributor');
        
        for ($i = 0; $i < 20; $i++){

            $program = new Program();
            $program->setTitle($faker->sentence());
            $program->setSynopsis($faker->paragraphs(3, true));
            $program->setCountry($faker->country());
            $program->setYear($faker->year());
            $program->setPoster('https://picsum.photos/seed/picsum/200/300');
            $randomCategoryKey = array_rand(CategoryFixtures::CATEGORIES);
            $categoryName = CategoryFixtures::CATEGORIES[$randomCategoryKey];
            $program->setCategory($this->getReference('category_' . $categoryName));
            $this->addReference('program_' . $i, $program);
            $user = $i % 2 === 0 ? $contributor : $admin;
            $program->setOwner($user);
            // Générer le slug à partir du titre
            $slug = $this->slugger->slug($program->getTitle())->lower();
            $program->setSlug($slug);
            $manager->persist($program);
        }
        
        $manager->flush();
    }
     public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          CategoryFixtures::class,
        ];
    }
}
