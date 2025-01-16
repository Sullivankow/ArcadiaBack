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


    //METHODE POST
    #[Route('/new', methods: ['POST'], name: 'create')]

    #[OA\Post(
        summary: "Créer un nouveau rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "date", type: "date", example: "Date du rapport"),
                    new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),
                    new OA\Property(property: "user_id", type: "integer", example: "id de l'utilisateur"),
                    new OA\Property(property: "animal_id", type: "integer", example: "id de l'animal"),

                ]
            )
        ),
        responses: [  // Utilisation correcte de 'responses' ici
            new OA\Response(
                response: 201,
                description: "Rappor vétérinaire créé avec succès", // Correction du message
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "date", type: "date", example: "Date du rapport"),
                        new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),

                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Rapport vétérinaire non trouvé" // Correction du message
            )
        ]
    )]








    public function new(Request $request): JsonResponse
    {
        //Création d'un objet utilisateur static en dur avec de fausses données pour tester l'api
// $utilisateur = new Utilisateur();
// $utilisateur->setUsername('testcrud@mail.com');
// $utilisateur->setPassword('Azerty_123');
// $utilisateur->setNom('koko');
// $utilisateur->setPrenom('jean');


        //Serialiszer transforme un format en un autre format
        $rapportVeterinaire = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');




        //On met l'objet sur liste d'attente avec persist puis on le push avec flush
        $this->manager->persist($rapportVeterinaire);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_rapportshow',

            ['id' => $rapportVeterinaire->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



        //à stocker en bdd
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }



    //METHODE GET

    #[Route('/show', 'show', methods: ['GET'])]

    #[OA\Get(
        summary: "Afficher la liste des rapports vétérinaire",
        tags: ["Rapport Vétérinaire"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Animal trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "date", type: "date", example: "Date du rapport"),
                        new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),
                        new OA\Property(property: "user_id", type: "integer", example: "id de l'utilisateur"),
                        new OA\Property(property: "animal_id", type: "integer", example: "id de l'animal"),


                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Rapport non trouvé"
            )
        ]
    )]







    public function show(): Response
    {
        $rapportVeterinaire = $this->rapportVeterinaireRepository->findAll();
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if ($rapportVeterinaire) {
            $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE PUT
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
                    new OA\Property(property: "date", type: "date", example: "Date du rapport vétérinaire"),
                    new OA\Property(property: "detail", type: "string", example: "Détail du rapport vétérinaire"),
                    new OA\Property(property: "user_id", type: "integer", example: "id de l'utilisateur"),
                    new OA\Property(property: "animal_id", type: "integer", example: "id de l'animal"),

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
                description: "Rapport vétérnaire non trouvé"
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

        // Modifier l'animal associé (si animal_id est fourni)
        if (isset($data['animal_id'])) {
            $animal = $this->animalRepository->find($data['animal_id']);
            if ($animal) {
                $rapportVeterinaire->setAnimal($animal);
            } else {
                return new JsonResponse(['message' => "Animal avec l'ID {$data['animal_id']} non trouvé"], Response::HTTP_BAD_REQUEST);
            }
        }

        // Modifier l'utilisateur associé (si user_id est fourni)
        if (isset($data['user_id'])) {
            $user = $this->userRepository->find($data['user_id']);
            if ($user) {
                $rapportVeterinaire->setUser($user);
            } else {
                return new JsonResponse(['message' => "Utilisateur avec l'ID {$data['user_id']} non trouvé"], Response::HTTP_BAD_REQUEST);
            }
        }

        // Persister les changements
        $this->manager->flush();

        // Retourner une réponse 200 avec les données mises à jour
        return new JsonResponse([
            'id' => $rapportVeterinaire->getId(),
            'date' => $rapportVeterinaire->getDate()->format('Y-m-d H:i:s'),
            'detail' => $rapportVeterinaire->getDetail(),
            'animal' => $rapportVeterinaire->getAnimal() ? $rapportVeterinaire->getAnimal()->getId() : null,
            'user' => $rapportVeterinaire->getUser() ? $rapportVeterinaire->getUser()->getId() : null,
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