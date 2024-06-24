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

    private const CATEGORIES = [
        "Languages" => [
            "PHP" => [
                "Superglobals" => [
                    '$_GET',
                    '$_POST',
                    '$_FILES',
                ],
                "File upload",
                "Arrays"
            ],
            "Rust" => [
                "Compilation",
                "Ownership",
                "Cargo",
                "Modules"
            ],
            "JS" => [
                "Closures",
                "Arrow functions",
                "ES6"
            ]
        ],
        "Frameworks" => [
            "NextJS",
            "Symfony",
            "Laravel"
        ]
    ];

    private const NB_ARTICLES = 50;

    private array $categories = [];

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

        $this->loadCategories($manager, null, self::CATEGORIES);

        // foreach (self::CATEGORIES as $parent => $children) {
        //     $category = new Category();
        //     $category->setName($parent);

        //     $manager->persist($category);
        //     $categories[] = $category;

        //     foreach ($children as $child => $subChildren) {

        //     }
        // }

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();

            $article
                ->setTitle($faker->words($faker->numberBetween(4, 9), true))
                ->setContent($faker->realTextBetween(200, 500))
                ->setVisible($faker->boolean(70))
                ->setCreatedAt($faker->dateTimeBetween('-4 years'))
                ->setAuthor($faker->randomElement($users))
                ->setCategory($faker->randomElement($this->categories));

            $manager->persist($article);
        }

        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager, ?Category $parentCategory, array $categories)
    {
        foreach ($categories as $key => $val) {
            $recursive = is_array($val);
            $categoryName = $recursive ? $key : $val;

            $category = new Category();
            $category
                ->setName($categoryName)
                ->setParent($parentCategory);
            $manager->persist($category);
            $this->categories[] = $category;

            if ($recursive) {
                $this->loadCategories($manager, $category, $val);
            }
        }
    }
}
