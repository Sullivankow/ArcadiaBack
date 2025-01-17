<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use App\Repository\HabitatRepository;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route('api/animal', name: 'app_api_animal')]

class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $animalRepository,
        private HabitatRepository $habitatRepository,
        private RaceRepository $raceRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    // METHODE POST
    #[Route('/new', name: 'create', methods: ['POST'])]

    #[OA\Post(
        summary: "Créer un nouvel animal",
        tags: ["Animal"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "prenom", type: "string", example: "Prénom de l'animal"),
                    new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                    new OA\Property(property: "habitat_id", type: "integer", example: 1),
                    new OA\Property(property: "race_id", type: "integer", example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Animal créé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "prenom", type: "string", example: "Prénom de l'animal"),
                        new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                        new OA\Property(property: "habitat_id", type: "integer", example: 1),
                        new OA\Property(property: "race_id", type: "integer", example: 1),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]

    public function new(Request $request): JsonResponse
    {
        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier que les informations nécessaires sont présentes
        if (!isset($data['prenom'], $data['etat'], $data['habitat_id'], $data['race_id'])) {
            return new JsonResponse(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        // Créer l'animal
        $animal = new Animal();
        $animal->setPrenom($data['prenom']);
        $animal->setEtat($data['etat']);

        // Récupérer l'habitat et la race à partir des IDs
        $habitat = $this->habitatRepository->find($data['habitat_id']);
        $race = $this->raceRepository->find($data['race_id']);

        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$race) {
            return new JsonResponse(['message' => 'Race not found'], Response::HTTP_NOT_FOUND);
        }

        // Associer l'habitat et la race à l'animal
        $animal->setHabitat($habitat);
        $animal->setRace($race);

        // Persist de l'animal dans la base de données
        $this->manager->persist($animal);
        $this->manager->flush();

        // Sérialiser l'animal créé pour la réponse, en utilisant les groupes de sérialisation
        $responseData = $this->serializer->serialize($animal, 'json', ['groups' => ['animal:read']]);

        // Générer l'URL de l'animal créé
        $location = $this->urlGenerator->generate(
            'app_api_animalshow',
            ['id' => $animal->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Retourner la réponse avec le code HTTP 201 et l'URL de la ressource créée
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    // METHODE GET (liste des animaux)
    #[Route('/list', name: 'list', methods: ['GET'])]

    #[OA\Get(
        summary: "Afficher la liste des animaux",
        tags: ["Animal"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des animaux récupérée avec succès",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "prenom", type: "string", example: "Nom de l'animal"),
                            new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                            new OA\Property(property: "habitat_id", type: "integer", example: 1),
                            new OA\Property(property: "race_id", type: "integer", example: 1),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Aucun animal trouvé"
            )
        ]
    )]

    public function list(): JsonResponse
    {
        $animals = $this->animalRepository->findAll();

        if (count($animals) > 0) {
            // Sérialiser la liste des animaux
            $responseData = $this->serializer->serialize($animals, 'json', ['groups' => ['animal:read']]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);





        }

        return new JsonResponse(['message' => 'No animals found'], Response::HTTP_NOT_FOUND);
    }






























    // METHODE GET
    #[Route('/show/{id}', 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Afficher Animal",
        tags: ["Animal"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'animal à afficher",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Animal trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "prenom", type: "string", example: "Nom de l'animal"),
                        new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]

    public function show(int $id): Response
    {
        $animal = $this->animalRepository->findOneBy(['id' => $id]);

        if ($animal) {
            // Sérialiser l'animal avec les groupes appropriés
            $responseData = $this->serializer->serialize($animal, 'json', ['groups' => ['animal:read']]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    // METHODE PUT
    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        summary: "Modifier animal",
        tags: ["Animal"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'animal à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "prenom", type: "string", example: "Prénom de l'animal"),
                    new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Animal modifié avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]

    public function edit(int $id, Request $request): JsonResponse
    {
        $animal = $this->animalRepository->findOneBy(['id' => $id]);

        if ($animal) {
            $animal = $this->serializer->deserialize(
                $request->getContent(),
                Animal::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $animal]
            );

            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    // METHODE DELETE
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer un animal",
        tags: ["Animal"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'animal à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Animal supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]

    public function delete(int $id): Response
    {
        $animal = $this->animalRepository->findOneBy(['id' => $id]);

        if (!$animal) {
            return new JsonResponse(['message' => 'Animal not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($animal);
        $this->manager->flush();
        return new JsonResponse(['message' => 'Animal deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
