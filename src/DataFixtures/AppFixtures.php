<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->posts = new ArrayCollection(); 
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User($this->passwordHasher);

        $user->setUsername("admin")->setPassword('password');

        $manager->persist($user);
        $manager->flush();
    }
}

