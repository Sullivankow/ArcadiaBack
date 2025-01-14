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
    // Définir le schéma Avis
    #[OA\Schema(
        type: 'object',
        properties: [
            new OA\Property(property: 'id', type: 'string', description: 'Identifiant de l\'avis'),
            new OA\Property(property: 'auteur', type: 'string', description: 'Nom de l\'auteur de l\'avis'),
            new OA\Property(property: 'contenu', type: 'string', description: 'Contenu de l\'avis'),
            new OA\Property(property: 'date', type: 'string', format: 'date-time', description: 'Date de création de l\'avis'),
            new OA\Property(property: 'valide', type: 'boolean', description: 'Indicateur si l\'avis est validé ou non'),
        ]
    )]
    public function getAvis(DocumentManager $dm): JsonResponse
    {
        // Récupère tous les avis dans MongoDB
        $avis = $dm->getRepository(Avis::class)->findAll();

        // Retourne la liste des avis en réponse JSON
        return $this->json($avis);
    }

    // Ajouter un nouvel avis
    #[Route('/new', methods: ['POST'])]
    #[OA\Post(
        summary: 'Ajoute un nouvel avis',
        tags: ['Avis'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['contenu'],
                properties: [
                    new OA\Property(property: 'auteur', type: 'string', description: 'Nom de l\'auteur de l\'avis'),
                    new OA\Property(property: 'contenu', type: 'string', description: 'Contenu de l\'avis')
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
        // Récupère les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Crée un nouvel avis à partir des données
        $avis = new Avis();
        $avis->setAuteur($data['auteur'] ?? 'Anonyme');
        $avis->setContenu($data['contenu']);
        $avis->setDate(new \DateTime());
        $avis->setValide(false);

        // Sauvegarde l'avis dans MongoDB
        $dm->persist($avis);
        $dm->flush();

        // Retourne une réponse de succès
        return $this->json(['message' => 'Avis ajouté avec succès'], 201);
    }

    // Valider un avis
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
        // Recherche l'avis par son ID
        $avis = $dm->getRepository(Avis::class)->find($id);

        if (!$avis) {
            // Si l'avis n'est pas trouvé, retourne une erreur 404
            return $this->json(['error' => 'Avis introuvable'], 404);
        }

        // Valide l'avis
        $avis->setValide(true);
        $dm->flush();

        // Retourne un message de succès
        return $this->json(['message' => 'Avis validé avec succès']);
    }

    // Supprimer un avis
    #[Route('/{id}', methods: ['DELETE'])]
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
        // Recherche l'avis par son ID
        $avis = $dm->getRepository(Avis::class)->find($id);

        if (!$avis) {
            // Si l'avis n'est pas trouvé, retourne une erreur 404
            return $this->json(['error' => 'Avis introuvable'], 404);
        }

        // Supprime l'avis
        $dm->remove($avis);
        $dm->flush();

        // Retourne un message de succès
        return $this->json(['message' => 'Avis supprimé avec succès']);
    }
}








