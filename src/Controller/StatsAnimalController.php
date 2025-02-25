<?php

namespace App\Controller;

use App\Document\StatsAnimal;
use App\Entity\Animal;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/stats', name: 'app_api_stats')]
class StatsAnimalController extends AbstractController
{
    private DocumentManager $dm;
    private EntityManagerInterface $em;

    public function __construct(DocumentManager $dm, EntityManagerInterface $em)
    {
        $this->dm = $dm;
        $this->em = $em;
    }

    #[Route('/click/{animalId}', methods: ['POST'])]
    #[OA\Post(
        path: "/api/stats/click/{animalId}",
        summary: "Incrémente le compteur de clics pour un animal",
        tags: ["Statistiques"],
        parameters: [
            new OA\Parameter(name: "animalId", in: "path", required: true, description: "L'ID de l'animal", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Stat mise à jour"),
            new OA\Response(response: 400, description: "Erreur de requête"),
        ]
    )]
    public function registerClick(int $animalId): JsonResponse
    {
        // Récupérer l'animal depuis MySQL
        $animal = $this->em->getRepository(Animal::class)->find($animalId);



        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], 404);
        }

        // Récupérer le prénom de l'animal
        $animalName = $animal->getPrenom();
        dump($animalName); // Vérifie le prénom récupéré

        // Récupérer la stat dans MongoDB
        $stat = $this->dm->getRepository(StatsAnimal::class)->findOneBy(['animalId' => $animalId]);

        if (!$stat) {
            $stat = new StatsAnimal();
            $stat->setAnimalId($animalId); // Assigner l'ID de l'animal
            $stat->setAnimalName($animalName); // Enregistrer le prénom
        }

        $stat->incrementClickCount();
        $this->dm->persist($stat);
        $this->dm->flush();

        return $this->json([
            'message' => 'Stat mise à jour',
            'animalId' => $stat->getAnimalId(),
            'animalName' => $stat->getAnimalName(),
            'clickCount' => $stat->getClickCount()
        ]);
    }



    #[Route('/all', methods: ['GET'])]
    #[OA\Get(
        path: "/api/stats/all",
        summary: "Récupère toutes les statistiques des clics des animaux",
        tags: ["Statistiques"],
        responses: [
            new OA\Response(response: 200, description: "Liste des statistiques"),
        ]
    )]
    public function getStats(): JsonResponse
    {
        $stats = $this->dm->getRepository(StatsAnimal::class)->findAll();
        
        // Récupérer les IDs des animaux pour les utiliser dans une seule requête
        $animalIds = array_map(fn($stat) => $stat->getAnimalId(), $stats);
        
        // Récupérer les animaux correspondants
        $animals = $this->em->getRepository(Animal::class)->findBy(['id' => $animalIds]);
        
        // Créer un tableau associatif pour un accès facile aux noms des animaux
        $animalNames = [];
        foreach ($animals as $animal) {
            $animalNames[$animal->getId()] = $animal->getPrenom();
        }
    
        $data = array_map(fn($stat) => [
            'animalId' => $stat->getAnimalId(),
            'animalName' => $animalNames[$stat->getAnimalId()] ?? 'Inconnu', // Récupérer le prénom de l'animal
            'clickCount' => $stat->getClickCount()
        ], $stats);
    
        return $this->json($data);
    }
    

    #[Route('/{animalId}', methods: ['GET'])]
    #[OA\Get(
        path: "/api/stats/{animalId}",
        summary: "Récupère les statistiques d'un animal spécifique",
        tags: ["Statistiques"],
        parameters: [
            new OA\Parameter(name: "animalId", in: "path", required: true, description: "L'ID de l'animal", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Statistiques de l'animal"),
            new OA\Response(response: 404, description: "Animal non trouvé"),
        ]
    )]
    public function getStatsByAnimal(int $animalId): JsonResponse
    {
        // Récupérer les statistiques de l'animal
        $stat = $this->dm->getRepository(StatsAnimal::class)->findOneBy(['animalId' => $animalId]);

        if (!$stat) {
            return $this->json(['message' => 'Statistiques non trouvées'], 404);
        }

        // Récupérer l'animal depuis MySQL pour obtenir son prénom
        $animal = $this->em->getRepository(Animal::class)->find($animalId);

        // Vérifiez si l'animal existe
        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], 404);
        }

        // Récupérer le prénom de l'animal
        $animalName = $animal->getPrenom();

        return $this->json([
            'animalId' => $stat->getAnimalId(),
            'animalName' => $animalName, // Utiliser le prénom récupéré
            'clickCount' => $stat->getClickCount()
        ]);
    }
}
    




