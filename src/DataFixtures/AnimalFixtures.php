<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\RapportVeterinaire;

class AnimalFixtures extends Fixture
{

 
    public function load(ObjectManager $manager): void   //Création de datafixtures(FAUSSES DONNEES) avec la relation avec RapportVeterinaire
    {

        for ($i = 1; $i <= 20; $i++) {
            /** @var RapportVeterinaire $rapportVeterinaire */
            
            $animal = (new Animal())
                ->setPrenom("Girafe $i")
                ->setEtat("Heureuse $i");
                
                

            

            $manager->persist($animal);
$this->addReference("animal$i", $animal); //Créer la référence animal

        }

        $manager->flush();
    }
}