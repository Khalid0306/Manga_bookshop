<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MangaFixtures extends Fixture
{

    function __construct(private CategoryRepository $categoryRepository)
    {}

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        // create 20 mangas! Bam!
        for ($i = 0; $i < 20; $i++) {
            $manga = new Manga();
            $manga->setTitle($faker->name());
            $manga->setPrice(mt_rand(3, 10));
            $manga->setCategory($this->categoryRepository->find(mt_rand(2, 4)));
            $manager->persist($manga);
        }

        $manager->flush();
    }
}