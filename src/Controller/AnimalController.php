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
#[OA\Tag(name: 'Animal', description: 'Gestion des animaux du zoo')]
class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $animalRepository,
        private HabitatRepository $habitatRepository,
       private RaceRepository $raceRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route('/new', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/animal/new',
        summary: 'Créer un nouvel animal',
        tags: ['Animal'],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'prenom', type: 'string', example: 'Lion'),
                        new OA\Property(property: 'etat', type: 'string', example: 'sain'),
                        new OA\Property(property: 'habitat', type: 'string', example: 'Savane Africaine'),
                        new OA\Property(property: 'race', type: 'string', example: 'Panthère'),
                        
                    ]
                )
            ]
        ),
        responses: [
            new OA\Response(response: '201', description: 'Animal créé avec succès'),
            new OA\Response(response: '400', description: 'Champs requis manquants'),
            new OA\Response(response: '404', description: 'Habitat ou race non trouvé'),
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifier si tous les champs requis sont présents
        if (!isset($data['prenom'], $data['etat'], $data['habitat'], $data['race'])) {
            return new JsonResponse(['message' => 'Champs requis manquants'], Response::HTTP_BAD_REQUEST);
        }

        // Récupération des entités liées
        $habitat = $this->habitatRepository->findOneBy(['nom' => $data['habitat']]);
        $race = $this->raceRepository->findOneBy(['label' => $data['race']]);

        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (!$race) {
            return new JsonResponse(['message' => 'Race non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Création d'un nouvel animal
        $animal = new Animal();
        $animal->setPrenom($data['prenom']);
        $animal->setEtat($data['etat']);
        $animal->setHabitat($habitat);
        $animal->setRace($race);

     

        // Persister les entités
        $this->manager->persist($animal);
        
        $this->manager->flush();

        // Sérialisation et création de la réponse
        $responseData = $this->serializer->serialize($animal, 'json', ['groups' => ['animal:read', 'habitat:read']]);
        $location = $this->urlGenerator->generate('app_api_animallist', ['id' => $animal->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }
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
                            new OA\Property(property: "habitat", type: "string", example: "Savane"),
                            new OA\Property(property: "race", type: "string", example: "Panthère"),
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
    
        if (count($animals) === 0) {
            return new JsonResponse(['message' => 'Aucun animal trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        // Créer un tableau pour stocker les informations des animaux
        $animalData = [];
        foreach ($animals as $animal) {
            $animalData[] = [
                'id' => $animal->getId(),
                'prenom' => $animal->getPrenom(),
                'etat' => $animal->getEtat(),
                'habitat' => $animal->getHabitat()->getNom(), // Récupérer le nom de l'habitat
                'race' => $animal->getRace()->getLabel(), // Récupérer le label de la race
            ];
        }
    
        // Convertir le tableau en JSON
        $jsonData = json_encode($animalData);
    
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
    

   // METHODE PUT
#[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
#[OA\Put(
    path: '/api/animal/edit/{id}',
    summary: 'Modifier un animal existant',
    tags: ['Animal'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID de l\'animal', schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'prenom', type: 'string', example: 'Lion'),
                    new OA\Property(property: 'etat', type: 'string', example: 'sain'),
                    new OA\Property(property: 'habitat', type: 'string', example: 'savane'),
                    new OA\Property(property: 'race', type: 'string', example: 'Panthère'),
                ]
            )
        ]
    ),
    responses: [
        new OA\Response(response: '204', description: 'Animal modifié avec succès'),
        new OA\Response(response: '404', description: 'Animal non trouvé'),
        new OA\Response(response: '400', description: 'Champs requis manquants'),
    ]
)]
public function edit(int $id, Request $request): JsonResponse
{
    // Recherche de l'animal par ID
    $animal = $this->animalRepository->find($id);

    if (!$animal) {
        return new JsonResponse(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    // Vérifier les champs requis
    if (!isset($data['prenom'], $data['etat'], $data['habitat'], $data['race'])) {
        return new JsonResponse(['message' => 'Champs requis manquants'], Response::HTTP_BAD_REQUEST);
    }

    // Récupération des entités associées (si nécessaire)
    $habitat = $this->habitatRepository->findOneBy(['nom' => $data['habitat']]);
    $race = $this->raceRepository->findOneBy(['label' => $data['race']]);

    if (!$habitat) {
        return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
    }

    if (!$race) {
        return new JsonResponse(['message' => 'Race non trouvée'], Response::HTTP_NOT_FOUND);
    }

    // Mettre à jour les propriétés de l'animal
    $animal->setPrenom($data['prenom']);
    $animal->setEtat($data['etat']);
    $animal->setHabitat($habitat);
    $animal->setRace($race);

    // Persister l'animal
    $this->manager->persist($animal);
    $this->manager->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
}


    // METHODE DELETE
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/animal/delete/{id}',
        summary: 'Supprimer un animal existant',
        tags: ['Animal'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID de l\'animal', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: '204', description: 'Animal supprimé avec succès'),
            new OA\Response(response: '404', description: 'Animal non trouvé'),
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $animal = $this->animalRepository->find($id);

        if (!$animal) {
            return new JsonResponse(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($animal);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Animal supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }
}


