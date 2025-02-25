<?php

namespace App\Controller;

use App\Document\StatsAnimal;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/stats', name: 'app_api_stats')]
class StatsAnimalController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/click/{animalId}', methods: ['POST'])]
    #[OA\Post(
        path: "/api/stats/click/{animalId}",
        summary: "Incrémente le compteur de clics pour un animal",
        tags: ["Statistiques"],
        parameters: [
            new OA\Parameter(name: "animalId", in: "path", required: true, description: "L'ID de l'animal", schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Stat mise à jour"),
            new OA\Response(response: 400, description: "Erreur de requête"),
        ]
    )]
    public function registerClick(string $animalId): JsonResponse
    {
        $stat = $this->dm->getRepository(StatsAnimal::class)->findOneBy(['animalId' => $animalId]);

        if (!$stat) {
            $stat = new StatsAnimal();
            $stat->setAnimalId($animalId);
        }

        $stat->incrementClickCount();
        $this->dm->persist($stat);
        $this->dm->flush();

        return $this->json([
            'message' => 'Stat mise à jour',
            'animalId' => $stat->getAnimalId(),
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
        $data = array_map(fn($stat) => [
            'animalId' => $stat->getAnimalId(),
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
            new OA\Parameter(name: "animalId", in: "path", required: true, description: "L'ID de l'animal", schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Statistiques de l'animal"),
            new OA\Response(response: 404, description: "Animal non trouvé"),
        ]
    )]
    public function getStatsByAnimal(string $animalId): JsonResponse
    {
        $stat = $this->dm->getRepository(StatsAnimal::class)->findOneBy(['animalId' => $animalId]);

        if (!$stat) {
            return $this->json(['message' => 'Animal non trouvé'], 404);
        }

        return $this->json([
            'animalId' => $stat->getAnimalId(),
            'clickCount' => $stat->getClickCount()
        ]);
    }
}

