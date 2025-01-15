<?php

namespace App\Controller;

use App\Document\Horaire;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/horaires', name: 'app_api_horaires')]
#[OA\Tag(name: "Horaire")]
class HoraireController extends AbstractController
{
    #[Route('/show', methods: ['GET'], name: 'list')]
    #[OA\Get(
        summary: "Liste des horaires",
        description: "Récupère tous les horaires du zoo.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des horaires récupérée avec succès",
                content: new OA\JsonContent(type: "array", items: new OA\Items(
                    properties: [
                        new OA\Property(property: "id", type: "string"),
                        new OA\Property(property: "jour", type: "string"),
                        new OA\Property(property: "heureOuverture", type: "string"),
                        new OA\Property(property: "heureFermeture", type: "string"),
                        new OA\Property(property: "saison", type: "string"),
                    ]
                ))
            )
        ]
    )]
    public function listHoraires(DocumentManager $dm): JsonResponse
    {
        $horaires = $dm->getRepository(Horaire::class)->findAll();
        $data = array_map(function (Horaire $horaire) {
            return [
                'id' => $horaire->getId(),
                'jour' => $horaire->getJour(),
                'heureOuverture' => $horaire->getHeureOuverture(),
                'heureFermeture' => $horaire->getHeureFermeture(),
                'saison' => $horaire->getSaison(),
            ];
        }, $horaires);

        return $this->json($data);
    }

    #[Route('/new', methods: ['POST'], name: 'create')]
    #[OA\Post(
        summary: "Créer un horaire",
        description: "Ajoute un nouvel horaire pour le zoo.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "jour", type: "string"),
                    new OA\Property(property: "heureOuverture", type: "string"),
                    new OA\Property(property: "heureFermeture", type: "string"),
                    new OA\Property(property: "saison", type: "string"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Horaire ajouté avec succès"),
            new OA\Response(response: 400, description: "Requête invalide")
        ]
    )]
    public function createHoraire(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $horaire = new Horaire();
        $horaire->setJour($data['jour'])
            ->setHeureOuverture($data['heureOuverture'])
            ->setHeureFermeture($data['heureFermeture'])
            ->setSaison($data['saison']);

        $dm->persist($horaire);
        $dm->flush();

        return $this->json(['message' => 'Horaire ajouté avec succès'], 201);
    }

    #[Route('/edit/{id}', methods: ['PUT'], name: 'edit')]
    #[OA\Put(
        summary: "Mettre à jour un horaire",
        description: "Met à jour un horaire existant via son ID.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "jour", type: "string", nullable: true),
                    new OA\Property(property: "heureOuverture", type: "string", nullable: true),
                    new OA\Property(property: "heureFermeture", type: "string", nullable: true),
                    new OA\Property(property: "saison", type: "string", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Horaire mis à jour avec succès"),
            new OA\Response(response: 404, description: "Horaire non trouvé")
        ]
    )]
    public function updateHoraire(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $horaire = $dm->getRepository(Horaire::class)->find($id);

        if (!$horaire) {
            return $this->json(['message' => 'Horaire non trouvé'], 404);
        }

        $horaire->setJour($data['jour'] ?? $horaire->getJour())
            ->setHeureOuverture($data['heureOuverture'] ?? $horaire->getHeureOuverture())
            ->setHeureFermeture($data['heureFermeture'] ?? $horaire->getHeureFermeture())
            ->setSaison($data['saison'] ?? $horaire->getSaison());

        $dm->flush();

        return $this->json(['message' => 'Horaire mis à jour avec succès']);
    }

    #[Route('/delete/{id}', methods: ['DELETE'], name: 'delete')]
    #[OA\Delete(
        summary: "Supprimer un horaire",
        description: "Supprime un horaire par son ID.",
        responses: [
            new OA\Response(response: 200, description: "Horaire supprimé avec succès"),
            new OA\Response(response: 404, description: "Horaire non trouvé")
        ]
    )]
    public function deleteHoraire(string $id, DocumentManager $dm): JsonResponse
    {
        $horaire = $dm->getRepository(Horaire::class)->find($id);

        if (!$horaire) {
            return $this->json(['message' => 'Horaire non trouvé'], 404);
        }

        $dm->remove($horaire);
        $dm->flush();

        return $this->json(['message' => 'Horaire supprimé avec succès']);
    }
}

