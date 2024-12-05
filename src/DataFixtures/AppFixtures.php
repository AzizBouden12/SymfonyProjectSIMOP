<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture

{
    public function __construct(private readonly UserPasswordHasherInterface $hasher){

    }


    public function load(ObjectManager $manager): void
    {
        $user = (new User());
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@admin.com')


            ->setPassword($this->hasher->hashPassword($user, 'admin'));
        $manager->persist($user);



        $manager->flush();
    }
}
