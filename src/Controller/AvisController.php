<?php

namespace App\Controller;

use App\Document\Avis;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/avis', name: 'app_api_avis')]
class AvisController extends AbstractController
{
    #[Route('/', methods: ['GET'])]
    #[OA\Get(
        summary: 'Récupère tous les avis',
        tags: ['Avis'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des avis',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', description: 'Identifiant de l\'avis'),
                            new OA\Property(property: 'auteur', type: 'string', description: 'Nom de l\'auteur de l\'avis'),
                            new OA\Property(property: 'contenu', type: 'string', description: 'Contenu de l\'avis'),
                            new OA\Property(property: 'date', type: 'string', format: 'date-time', description: 'Date de création de l\'avis'),
                            new OA\Property(property: 'valide', type: 'boolean', description: 'Indicateur si l\'avis est validé ou non'),
                            new OA\Property(property: 'note', type: 'integer', description: 'Note donnée par le visiteur (1 à 5)'),
                        ]
                    )
                )
            )
        ]
    )]
    public function getAvis(DocumentManager $dm): JsonResponse
    {
        $avis = $dm->getRepository(Avis::class)->findAll();

        // Transforme les objets en tableau pour la réponse JSON
        $avisArray = array_map(fn($a) => [
            'id' => $a->getId(),
            'auteur' => $a->getAuteur(),
            'contenu' => $a->getContenu(),
            'date' => $a->getDate()?->format('Y-m-d H:i:s'),
            'valide' => $a->isValide(),
            'note' => $a->getNote(),
        ], $avis);

        return $this->json($avisArray);
    }

    #[Route('/new', methods: ['POST'])]
    #[OA\Post(
        summary: 'Ajoute un nouvel avis',
        tags: ['Avis'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['contenu', 'note'],
                properties: [
                    new OA\Property(property: 'auteur', type: 'string', description: 'Nom de l\'auteur de l\'avis'),
                    new OA\Property(property: 'contenu', type: 'string', description: 'Contenu de l\'avis'),
                    new OA\Property(property: 'note', type: 'integer', description: 'Note donnée par le visiteur (1 à 5)')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Avis ajouté avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function addAvis(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Valide la note
        if (!isset($data['note']) || $data['note'] < 1 || $data['note'] > 5) {
            return $this->json(['error' => 'La note doit être comprise entre 1 et 5.'], 400);
        }

        $avis = new Avis();
        $avis->setAuteur($data['auteur'] ?? 'Anonyme');
        $avis->setContenu($data['contenu']);
        $avis->setDate(new \DateTime());
        $avis->setValide(false);
        $avis->setNote($data['note']);

        $dm->persist($avis);
        $dm->flush();

        return $this->json(['message' => 'Avis ajouté avec succès'], 201);
    }

    #[Route('/{id}/validate', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Valide un avis par son ID',
        tags: ['Avis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID de l\'avis à valider',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Avis validé avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Avis non trouvé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function validateAvis(string $id, DocumentManager $dm): JsonResponse
    {
        $avis = $dm->getRepository(Avis::class)->find($id);

        if (!$avis) {
            return $this->json(['error' => 'Avis introuvable'], 404);
        }

        $avis->setValide(true);
        $dm->flush();

        return $this->json(['message' => 'Avis validé avec succès']);
    }

    #[Route('/{id}/supprimer', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Supprime un avis par son ID',
        tags: ['Avis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID de l\'avis à supprimer',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Avis supprimé avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Avis non trouvé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function deleteAvis(string $id, DocumentManager $dm): JsonResponse
    {
        $avis = $dm->getRepository(Avis::class)->find($id);

        if (!$avis) {
            return $this->json(['error' => 'Avis introuvable'], 404);
        }

        $dm->remove($avis);
        $dm->flush();

        return $this->json(['message' => 'Avis supprimé avec succès']);
    }
}