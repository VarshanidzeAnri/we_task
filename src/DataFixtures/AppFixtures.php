<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@gmail.com');

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '123'
        );

        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $category = new Category();
        $category->setTitle('cat1');
        $manager->persist($category);

        $category = new Category();
        $category->setTitle('cat2');
        $manager->persist($category);

        $category = new Category();
        $category->setTitle('cat3');
        $manager->persist($category);

        $manager->flush();
    }
}
