<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ArticleFixture extends Fixture implements DependentFixtureInterface
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        // récupère tous les utilisateurs
        $userList = $this->userRepository->findAll();

        for ($i = 0; $i < 20; $i++) {
            // prends un index aléatoire dans le tableau
            $userIndex = $faker->numberBetween(0, count($userList)-1);
            $article = new Article();
            $article->setTitle($faker->word);
            $article->setContent($faker->text);
            $article->setUser($userList[$userIndex]);
            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
