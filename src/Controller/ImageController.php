<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use App\Repository\HabitatRepository;

#[Route('api/image', name: 'app_api_image')]
class ImageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ImageRepository $imageRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        private HabitatRepository $habitatRepository,
    ) {
    }

    // Méthode POST pour ajouter une image
    #[Route('/new', methods: ['POST'])]
    #[OA\Post(
        summary: "Ajouter une nouvelle image",
        tags: ["Image"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "image_data", type: "string", example: "aquatique-habitat.jpg"),
                    new OA\Property(property: "habitat_id", type: "integer", example: 3)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Image ajoutée avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "image_data", type: "string", example: "aquatique-habitat.jpg"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Habitat non trouvé"
            ),
            new OA\Response(
                response: 400,
                description: "Requête invalide"
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        // Récupérer le contenu JSON
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données nécessaires sont présentes
        if (!isset($data['image_data']) || !isset($data['habitat_id'])) {
            return new JsonResponse(['error' => 'Les champs "image_data" et "habitat_id" sont requis'], Response::HTTP_BAD_REQUEST);
        }

        // Trouver l'habitat correspondant à l'ID
        $habitat = $this->habitatRepository->find($data['habitat_id']);
        if (!$habitat) {
            return new JsonResponse(['error' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Créer une nouvelle entité Image
        $image = new Image();
        $image->setImagePath($data['image_data']);  // Enregistrer le chemin du fichier dans image_path
        $image->addHabitat($habitat); // Utiliser addHabitat() pour lier l'habitat à l'image

        // Persister et enregistrer l'image en base de données
        $this->manager->persist($image);
        $this->manager->flush(); //  pas besoin de persister l'habitat, il est déjà géré dans la relation ManyToMany

        // Sérialiser l'image pour la réponse JSON
        $responseData = $this->serializer->serialize($image, 'json');

        // Créer l'URL pour l'image
        $location = $this->urlGenerator->generate(
            'app_api_imageshow',  //  la route pour l'image doit être définie correctement dans la méthode GET
            ['id' => $image->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Retourne la réponse JSON avec l'image ajoutée et son URL
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    // Méthode GET pour afficher une image
    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Afficher l'image",
        tags: ["Image"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'image à afficher",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Image trouvée avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "image_data", type: "string", example: "aquatique-habitat.jpg"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvée"
            )
        ]
    )]
    public function show(int $id): Response
    {
        $image = $this->imageRepository->findOneBy(['id' => $id]);
        if ($image) {
            $responseData = $this->serializer->serialize($image, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    // Méthode PUT pour modifier une image
    #[Route('/update/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        summary: "Modifier l'image",
        tags: ["Image"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'image à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "image_data", type: "string", example: "nouvelle-image.jpg"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Image modifiée avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvée"
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $image = $this->imageRepository->findOneBy(['id' => $id]);

        if ($image) {
            // Désérialiser les données de la requête
            $data = json_decode($request->getContent(), true);
            $image = $this->serializer->deserialize(
                $request->getContent(),
                Image::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $image]
            );

            // Si 'image_data' a changé, on met à jour 'image_path'
            if (isset($data['image_data'])) {
                $image->setImagePath($data['image_data']);
            }

            // Sauvegarder les modifications
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    // Méthode DELETE pour supprimer une image
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer une image",
        tags: ["Image"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'image à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Image supprimée avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvée"
            )
        ]
    )]
    public function delete(int $id): Response
    {
        $image = $this->imageRepository->findOneBy(['id' => $id]);
        if (!$image) {
            return new JsonResponse(['error' => "Aucune image trouvée pour l'ID {$id}"], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($image);
        $this->manager->flush();
        return new JsonResponse(['message' => 'Image supprimée avec succès'], Response::HTTP_NO_CONTENT);
    }
}


