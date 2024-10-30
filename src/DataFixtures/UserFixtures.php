<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {

    }
    public function load(ObjectManager $manager): void   //Création de datafixtures(FAUSES DONNEES)
    {
      
for($i=1; $i <= 20; $i++) {
$user = (new User())
->setEmail("email.$i@ecf.fr")
->setNom("Dupont $i")
->setPrenom("Robert $i");

$user->setPassword($this->userPasswordHasher->hashPassword($user, "password$i"));

$manager->persist($user);
}

        $manager->flush();
    }
}
