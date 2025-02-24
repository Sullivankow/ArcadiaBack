<?php

namespace App\Controller;
use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use App\Repository\AnimalRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;





#[Route('api/rapport', name: 'app_api_rapport')]
class RapportVeterinaireController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $rapportVeterinaireRepository,
        private AnimalRepository $animalRepository,
        private UserRepository $userRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {

    }


    #[Route('/new', methods: ['POST'], name: 'create')]
    #[OA\Post(
        summary: "Créer un nouveau rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "date", type: "string", format: "date", example: "22-02-2025"),
                    new OA\Property(property: "detail", type: "string", example: "Examen de routine"),
                    new OA\Property(property: "email", type: "string", example: "veto@example.com"),
                    new OA\Property(property: "animal_prenom", type: "string", example: "Rex")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Rapport vétérinaire créé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "date", type: "string", format: "date", example: "22-02-2025"),
                        new OA\Property(property: "detail", type: "string", example: "Examen de routine"),
                        new OA\Property(property: "user_email", type: "string", example: "veto@example.com"),
                        new OA\Property(property: "animal_prenom", type: "string", example: "Rex")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Données incomplètes"
            ),
            new OA\Response(
                response: 404,
                description: "Utilisateur ou animal non trouvé"
            )
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['email'], $data['animal_prenom'], $data['date'], $data['detail'])) {
            return $this->json(['message' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }
    
        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $animal = $this->animalRepository->findOneBy(['prenom' => $data['animal_prenom']]);
        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $rapport = new RapportVeterinaire();
        $rapport->setDate(new \DateTime($data['date']));
        $rapport->setDetail($data['detail']);
        $rapport->setUser($user);
        $rapport->setAnimal($animal);
    
        $this->manager->persist($rapport);
        $this->manager->flush();
    
        return $this->json([
            'id' => $rapport->getId(),
            'date' => $rapport->getDate()->format('y-m-d'),
            'detail' => $rapport->getDetail(),
            'user_email' => $user->getEmail(),
            'animal_prenom' => $animal->getPrenom(),
        ], Response::HTTP_CREATED);
    }
    



    





// METHODE GET
#[Route('/show', 'show', methods: ['GET'])]
#[OA\Get(
    summary: "Afficher la liste des rapports vétérinaires",
    tags: ["Rapport Vétérinaire"],
    responses: [
        new OA\Response(
            response: 200,
            description: "Rapports trouvés avec succès",
            content: new OA\JsonContent(
                type: "array",
                items: new OA\Items(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "date", type: "date", example: "22-02-2025"),
                        new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),
                        new OA\Property(property: "userEmail", type: "string", example: "email@example.com"),
                        new OA\Property(property: "animalName", type: "string", example: "Nom de l'animal"),
                    ]
                )
            )
        ),
        new OA\Response(
            response: 404,
            description: "Aucun rapport trouvé"
        )
    ]
)]
public function show(): Response
{
    $rapportsVeterinaires = $this->rapportVeterinaireRepository->findAll();

    if ($rapportsVeterinaires) {
        $responseData = [];
        
        foreach ($rapportsVeterinaires as $rapport) {
            $responseData[] = [
                'id' => $rapport->getId(),
                'date' => $rapport->getDate()->format('d-m-y'),
                'detail' => $rapport->getDetail(),
                'userEmail' => $rapport->getUser() ? $rapport->getUser()->getEmail() : null, // Récupérer l'email de l'utilisateur
                'animalName' => $rapport->getAnimal() ? $rapport->getAnimal()->getPrenom() : null, // Récupérer le prénom de l'animal
            ];
        }

        return new JsonResponse($responseData, Response::HTTP_OK);
    }

    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
}








// METHODE PUT
#[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
#[OA\Put(
    summary: "Modifier le rapport vétérinaire",
    tags: ["Rapport Vétérinaire"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID du rapport vétérinaire à modifier",
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "animal_prenom", type: "string", example: "Prénom de l'animal"),
                new OA\Property(property: "detail", type: "string", example: "Détail du rapport vétérinaire"),
                new OA\Property(property: "user_email", type: "string", example: "Email de l'utilisateur"),
                new OA\Property(property: "date", type: "date", example: "Date du rapport vétérinaire"),
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: "Rapport vétérinaire modifié avec succès"
        ),
        new OA\Response(
            response: 404,
            description: "Rapport vétérinaire non trouvé"
        )
    ]
)]
public function edit(int $id, Request $request): JsonResponse
{
    // Récupérer le rapport vétérinaire à modifier
    $rapportVeterinaire = $this->rapportVeterinaireRepository->find($id);

    if (!$rapportVeterinaire) {
        return new JsonResponse(['message' => 'Rapport vétérinaire non trouvé'], Response::HTTP_NOT_FOUND);
    }

    // Décoder les données JSON de la requête
    $data = json_decode($request->getContent(), true);

    if (!$data) {
        return new JsonResponse(['message' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
    }

    // Modifier les champs simples
    if (isset($data['date'])) {
        try {
            $date = new \DateTime($data['date']);
            $rapportVeterinaire->setDate($date);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
        }
    }

    if (isset($data['detail'])) {
        $rapportVeterinaire->setDetail($data['detail']);
    }

    // Modifier l'animal associé (si animal_prenom est fourni)
    if (isset($data['animal_prenom'])) {
        $animal = $this->animalRepository->findOneBy(['prenom' => $data['animal_prenom']]);
        if ($animal) {
            $rapportVeterinaire->setAnimal($animal);
        } else {
            return new JsonResponse(['message' => "Animal avec le prénom {$data['animal_prenom']} non trouvé"], Response::HTTP_BAD_REQUEST);
        }
    }

    // Modifier l'utilisateur associé (si user_email est fourni)
    if (isset($data['user_email'])) {
        $user = $this->userRepository->findOneBy(['email' => $data['user_email']]);
        if ($user) {
            $rapportVeterinaire->setUser($user);
        } else {
            return new JsonResponse(['message' => "Utilisateur avec l'email {$data['user_email']} non trouvé"], Response::HTTP_BAD_REQUEST);
        }
    }

    // Persister les changements
    $this->manager->flush();

    // Retourner une réponse 200 avec les données mises à jour
    return new JsonResponse([
        'id' => $rapportVeterinaire->getId(),
        'animal_prenom' => $rapportVeterinaire->getAnimal() ? $rapportVeterinaire->getAnimal()->getPrenom() : null,
        'detail' => $rapportVeterinaire->getDetail(),
        'user_email' => $rapportVeterinaire->getUser() ? $rapportVeterinaire->getUser()->getEmail() : null,
        'date' => $rapportVeterinaire->getDate()->format('Y-m-d H:i:s'),
    ], Response::HTTP_OK);
}






    //METHODE DELETE

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer un rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du rapport vétérinaire à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Rapport vétérinaire supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Rapport vétérinaire non trouvé"
            )
        ]
    )]




    public function delete(int $id): JsonResponse
    {
        // Récupérer le rapport vétérinaire à supprimer
        $rapportVeterinaire = $this->rapportVeterinaireRepository->findOneBy(['id' => $id]);

        // Si le rapport vétérinaire n'est pas trouvé, renvoyer une réponse 404 Not Found
        if (!$rapportVeterinaire) {
            return new JsonResponse(['message' => "No report found for ID {$id}"], Response::HTTP_NOT_FOUND);
        }

        // Supprimer le rapport vétérinaire
        $this->manager->remove($rapportVeterinaire);
        $this->manager->flush();

        // Retourner une réponse 204 No Content (pas de contenu à renvoyer)
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
