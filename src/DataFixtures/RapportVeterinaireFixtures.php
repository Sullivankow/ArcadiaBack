<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\RapportVeterinaire;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class RapportVeterinaireFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager): void   //Création de datafixtures(FAUSSES DONNEES)
    {

        for ($i = 1; $i <= 20; $i++) {
            /** @var Animal $animal */
            $animal = $this->getReference("animal" . random_int(1, 20)); //Récupère la référence animal
            
            $rapportVeterinaire = (new RapportVeterinaire())
                ->setDate(date_create_immutable())
                ->setDetail("a mangé 3 repas $i")
                ->setAnimal($animal);



            $manager->persist($rapportVeterinaire);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [AnimalFixtures::class];
    }
}