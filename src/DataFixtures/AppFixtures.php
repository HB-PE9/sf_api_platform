<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const USERS = [
        "admin@outlook.fr" => "admin",
        "martin@gmail.com" => "martin",
        "bobby@bob.net" => "bobby",
        "test@test.com" => "test"
    ];

    private const CATEGORIES = ["NextJS", "Rust", "WebAssembly", "PHP", "Symfony"];

    private const NB_ARTICLES = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $users = [];

        foreach (self::USERS as $email => $password) {
            $user = new User();
            $user
                ->setEmail($email)
                ->setPassword($password);

            if (str_contains($email, 'admin')) {
                $user->setRoles(["ROLE_ADMIN"]);
            }

            $manager->persist($user);
            $users[] = $user;
        }

        $categories = [];

        foreach (self::CATEGORIES as $catName) {
            $category = new Category();
            $category->setName($catName);

            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();

            $article
                ->setTitle($faker->words($faker->numberBetween(4, 9), true))
                ->setContent($faker->realTextBetween(200, 500))
                ->setVisible($faker->boolean(70))
                ->setCreatedAt($faker->dateTimeBetween('-4 years'))
                ->setAuthor($faker->randomElement($users))
                ->setCategory($faker->randomElement($categories));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
