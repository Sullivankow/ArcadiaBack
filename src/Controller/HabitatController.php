<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Entity\Image;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('api/habitat', name: 'app_api_habitat')]
class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $habitatRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    // METHODE POST - Créer un nouvel habitat
    #[Route(methods: ['POST'])]
    #[OA\Post(
        summary: "Créer un nouvel habitat",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Nom de l'habitat"),
                    new OA\Property(property: "description", type: "string", example: "Description de l'habitat"),
                    new OA\Property(property: "commentaire_habitat", type: "string", example: "Commentaire sur l'habitat"),
                    new OA\Property(property: "image_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Habitat créé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "nom", type: "string", example: "Nom de l'habitat"),
                        new OA\Property(property: "description", type: "string", example: "Description de l'habitat"),
                        new OA\Property(property: "commentaire_habitat", type: "string", example: "Commentaire sur l'habitat")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Données invalides")
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Désérialiser les données de l'habitat
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:write'],
        ]);

        // Si un image_id est fourni, récupérer l'image correspondante
        if (isset($data['image_id'])) {
            $image = $this->manager->getRepository(Image::class)->find($data['image_id']);

            if ($image) {
                $habitat->addImage($image);  // Associer l'image à l'habitat
            }
        }

        // Persister l'habitat
        $this->manager->persist($habitat);
        $this->manager->flush();

        // Sérialiser la réponse
        $responseData = $this->serializer->serialize($habitat, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:read'],
        ]);

        // Générer l'URL de l'habitat créé
        $location = $this->urlGenerator->generate(
            'app_api_habitatshow',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    // METHODE PUT - Modifier habitat
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        summary: "Modifier habitat",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'habitat à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Nom de l'habitat"),
                    new OA\Property(property: "description", type: "string", example: "Description de l'habitat"),
                    new OA\Property(property: "commentaire_habitat", type: "string", example: "Commentaire sur l'habitat"),
                    new OA\Property(property: "image_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Habitat modifié avec succès"),
            new OA\Response(response: 404, description: "Habitat non trouvé")
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->habitatRepository->find($id);

        if ($habitat) {
            // Désérialiser les données envoyées et les appliquer à l'entité existante
            $this->serializer->deserialize(
                $request->getContent(),
                Habitat::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $habitat, // Mettre à jour l'objet existant
                    AbstractNormalizer::GROUPS => ['habitat:write']
                ]
            );

            // Vérifiez si une image est envoyée et associez-la
            $data = json_decode($request->getContent(), true);
            if (isset($data['image_id'])) {
                $image = $this->manager->getRepository(Image::class)->find($data['image_id']);
                if ($image) {
                    $habitat->addImage($image);
                }
            }

            // Enregistrer les modifications dans la base de données
            $this->manager->flush();

            // Sérialiser la réponse avec les nouvelles données
            $responseData = $this->serializer->serialize($habitat, 'json', [
                AbstractNormalizer::GROUPS => ['habitat:read'],
            ]);

            // Retourner la réponse avec les données mises à jour
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
    }
}
