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
    #[Route('/new', methods: ['POST'], name: 'new')]
    #[OA\Post(
        summary: "Créer un nouvel habitat",
        tags: ["Habitat"],
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

        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:write'],
        ]);

        if (isset($data['image_id'])) {
            $image = $this->manager->getRepository(Image::class)->find($data['image_id']);

            if ($image) {
                $habitat->addImage($image);
            }
        }

        $this->manager->persist($habitat);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($habitat, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:read'],
        ]);

        $location = $this->urlGenerator->generate(
            'app_api_habitatlist',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    // METHODE PUT - Modifier habitat
    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        summary: "Modifier un habitat",
        tags: ["Habitat"],
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
            $this->serializer->deserialize(
                $request->getContent(),
                Habitat::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $habitat,
                    AbstractNormalizer::GROUPS => ['habitat:write']
                ]
            );

            $data = json_decode($request->getContent(), true);
            if (isset($data['image_id'])) {
                $image = $this->manager->getRepository(Image::class)->find($data['image_id']);
                if ($image) {
                    $habitat->addImage($image);
                }
            }

            $this->manager->flush();

            $responseData = $this->serializer->serialize($habitat, 'json', [
                AbstractNormalizer::GROUPS => ['habitat:read'],
            ]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
    }

    // METHODE GET - Liste des habitats
    #[Route('/show', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Récupérer la liste des habitats",
        tags: ["Habitat"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des habitats récupérée avec succès",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nom", type: "string", example: "Savane Africaine"),
                            new OA\Property(property: "description", type: "string", example: "Habitat pour les animaux de la savane"),
                            new OA\Property(property: "commentaire_habitat", type: "string", example: "Habitat spacieux et bien entretenu")
                        ]
                    )
                )
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $habitats = $this->habitatRepository->findAllWithRelations();

        $responseData = $this->serializer->serialize($habitats, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:read'],
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    // METHODE DELETE - Supprimer un habitat
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer un habitat",
        tags: ["Habitat"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'habitat à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Habitat supprimé avec succès"),
            new OA\Response(response: 404, description: "Habitat non trouvé")
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->habitatRepository->find($id);

        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($habitat);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Habitat supprimé avec succès'], Response::HTTP_OK);
    }
}


